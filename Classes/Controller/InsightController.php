<?php

declare(strict_types=1);

namespace Indiz\Products\Controller;

use Indiz\Products\Domain\Model\Insight;
use Indiz\Products\Domain\Repository\InsightRepository;
use Indiz\Products\Domain\Repository\InsighttagRepository;
use Indiz\Products\Domain\Repository\InsightcategoryRepository;
use Indiz\Products\Services\Mailer;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\Search\FileSearchDemand;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\Exception\FolderDoesNotExistException ;


class InsightController extends ActionController
{
    public function __construct(
        protected readonly InsightRepository $insightRepository,
        protected readonly InsighttagRepository $insighttagRepository,
        protected readonly InsightcategoryRepository $insightcategoryRepository,
        private readonly PersistenceManager $persistenceManager
    ) {}

    private $imagenames = [];

    public function filterAction(): \Psr\Http\Message\ResponseInterface
    {
        $allowed = ['categories', 'tags', 'searchquery', 'pagesize', 'page'];
        $filter = [];
        foreach ($allowed as $key) {
            if ($this->request->hasArgument($key)) {
                $filter[$key] = $this->request->getArgument($key);
            }
        }
        $feUser = $this->request->getAttribute('frontend.user');
        $feUser->setKey('ses', 'insightFilter', $filter);
        $feUser->storeSessionData();

        return $this->redirect('index');
    }

    public function copyImages(): array
    {
        $conn = GeneralUtility::makeInstance(ConnectionPool::class);

        // Load all language-1 records that have a default-language parent
        $qb = $conn->getQueryBuilderForTable('tx_products_domain_model_insight');
        $qb->getRestrictions()->removeAll();
        $translations = $qb
            ->select('uid', 'l10n_parent')
            ->from('tx_products_domain_model_insight')
            ->where(
                $qb->expr()->eq('sys_language_uid', $qb->createNamedParameter(1)),
                $qb->expr()->gt('l10n_parent', $qb->createNamedParameter(0)),
                $qb->expr()->eq('deleted', $qb->createNamedParameter(0))
            )
            ->executeQuery()
            ->fetchAllAssociative();

        $stats = ['copied' => 0, 'skipped' => 0, 'errors' => []];

        foreach ($translations as $translation) {
            $sourceUid = (int)$translation['uid'];
            $targetUid = (int)$translation['l10n_parent'];

            // Load all file references from the language-1 record
            $qbRef = $conn->getQueryBuilderForTable('sys_file_reference');
            $qbRef->getRestrictions()->removeAll();
            $references = $qbRef
                ->select('uid', 'uid_local', 'fieldname', 'pid', 'sorting_foreign', 'title', 'description', 'alternative')
                ->from('sys_file_reference')
                ->where(
                    $qbRef->expr()->eq('uid_foreign', $qbRef->createNamedParameter($sourceUid)),
                    $qbRef->expr()->eq('tablenames', $qbRef->createNamedParameter('tx_products_domain_model_insight')),
                    $qbRef->expr()->eq('deleted', $qbRef->createNamedParameter(0))
                )
                ->executeQuery()
                ->fetchAllAssociative();

            if (empty($references)) {
                continue;
            }

            // Collect already-existing references on the default-language record to avoid duplicates
            $qbExist = $conn->getQueryBuilderForTable('sys_file_reference');
            $qbExist->getRestrictions()->removeAll();
            $existing = $qbExist
                ->select('uid_local', 'fieldname')
                ->from('sys_file_reference')
                ->where(
                    $qbExist->expr()->eq('uid_foreign', $qbExist->createNamedParameter($targetUid)),
                    $qbExist->expr()->eq('tablenames', $qbExist->createNamedParameter('tx_products_domain_model_insight')),
                    $qbExist->expr()->eq('deleted', $qbExist->createNamedParameter(0))
                )
                ->executeQuery()
                ->fetchAllAssociative();

            $existingKeys = [];
            foreach ($existing as $e) {
                $existingKeys[$e['fieldname'] . '_' . $e['uid_local']] = true;
            }

            $fieldCounts = [];
            foreach ($references as $ref) {
                $key = $ref['fieldname'] . '_' . $ref['uid_local'];
                if (isset($existingKeys[$key])) {
                    $stats['skipped']++;
                    continue;
                }

                try {
                    $conn->getConnectionForTable('sys_file_reference')->insert('sys_file_reference', [
                        'tstamp'           => time(),
                        'crdate'           => time(),
                        'uid_local'        => (int)$ref['uid_local'],
                        'uid_foreign'      => $targetUid,
                        'tablenames'       => 'tx_products_domain_model_insight',
                        'fieldname'        => $ref['fieldname'],
                        'pid'              => (int)$ref['pid'],
                        'l10n_parent'      => 0,
                        'sys_language_uid' => 0,
                        'sorting_foreign'  => (int)$ref['sorting_foreign'],
                        'title'            => $ref['title'] ?? '',
                        'description'      => $ref['description'] ?? '',
                        'alternative'      => $ref['alternative'] ?? '',
                    ]);
                    $fieldCounts[$ref['fieldname']] = ($fieldCounts[$ref['fieldname']] ?? 0) + 1;
                    $stats['copied']++;
                } catch (\Throwable $e) {
                    $stats['errors'][] = "source={$sourceUid} target={$targetUid} uid_local={$ref['uid_local']}: " . $e->getMessage();
                }
            }

            // Keep the FAL count columns on the default-language record in sync
            foreach ($fieldCounts as $fieldName => $count) {
                $conn->getConnectionForTable('tx_products_domain_model_insight')->update(
                    'tx_products_domain_model_insight',
                    [$fieldName => $count],
                    ['uid' => $targetUid]
                );
            }

            // Copy MM relations (categories + tags) from source to target
            foreach ([
                'tx_products_domain_model_insight_insightcategory_mm',
                'tx_products_domain_model_insight_insighttag_mm',
            ] as $mmTable) {
                $this->copyMmRelations($conn, $mmTable, $sourceUid, $targetUid, $stats);
            }
        }
        
        return $stats;
    }

    public function syncCategories(): array
    {
        $conn = GeneralUtility::makeInstance(ConnectionPool::class);
        $mmTable = 'tx_products_domain_model_insight_insightcategory_mm';
        $stats = ['created' => 0, 'skipped' => 0, 'errors' => []];

        // Load all language-1 insighttag records that have a default-language parent
        $qb = $conn->getQueryBuilderForTable('tx_products_domain_model_insight');
        $qb->getRestrictions()->removeAll();
        $tags = $qb
            ->select('uid', 'l10n_parent')
            ->from('tx_products_domain_model_insight')
            ->where(
                $qb->expr()->eq('sys_language_uid', $qb->createNamedParameter(1)),
                $qb->expr()->gt('l10n_parent', $qb->createNamedParameter(0)),
                $qb->expr()->eq('deleted', $qb->createNamedParameter(0))
            )
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($tags as $tag) {
            $tagUid       = (int)$tag['uid'];
            $parentUid    = (int)$tag['l10n_parent'];

            // Check if MM rows exist for the translated record (uid_local = tag uid)
            $qbMm = $conn->getQueryBuilderForTable($mmTable);
            $mmRows = $qbMm
                ->select('uid_local', 'uid_foreign', 'sorting', 'sorting_foreign')
                ->from($mmTable)
                ->where($qbMm->expr()->eq('uid_local', $qbMm->createNamedParameter($tagUid)))
                ->executeQuery()
                ->fetchAllAssociative();

            if (empty($mmRows)) {
                continue;
            }

            // Collect already-existing uid_foreign values for the parent (uid_local = l10n_parent)
            $qbExist = $conn->getQueryBuilderForTable($mmTable);
            $existingForeign = $qbExist
                ->select('uid_foreign')
                ->from($mmTable)
                ->where($qbExist->expr()->eq('uid_local', $qbExist->createNamedParameter($parentUid)))
                ->executeQuery()
                ->fetchFirstColumn();

            $existingSet = array_flip($existingForeign);

            foreach ($mmRows as $mmRow) {
                $uidForeign = (int)$mmRow['uid_foreign'];

                if (isset($existingSet[$uidForeign])) {
                    $stats['skipped']++;
                    continue;
                }

                try {
                    $conn->getConnectionForTable($mmTable)->insert($mmTable, [
                        'uid_local'       => $parentUid,
                        'uid_foreign'     => $uidForeign,
                        'sorting'         => (int)$mmRow['sorting'],
                        'sorting_foreign' => (int)($mmRow['sorting_foreign'] ?? 0),
                    ]);
                    $stats['created']++;
                } catch (\Throwable $e) {
                    $stats['errors'][] = "tag={$tagUid} parent={$parentUid} uid_foreign={$uidForeign}: " . $e->getMessage();
                }
            }
        }

        return $stats;
    }

    public function syncTags(): array
    {
        $conn = GeneralUtility::makeInstance(ConnectionPool::class);
        $mmTable = 'tx_products_domain_model_insight_insighttag_mm';
        $stats = ['created' => 0, 'skipped' => 0, 'errors' => []];

        // Load all language-1 insighttag records that have a default-language parent
        $qb = $conn->getQueryBuilderForTable('tx_products_domain_model_insight');
        $qb->getRestrictions()->removeAll();
        $tags = $qb
            ->select('uid', 'l10n_parent')
            ->from('tx_products_domain_model_insight')
            ->where(
                $qb->expr()->eq('sys_language_uid', $qb->createNamedParameter(1)),
                $qb->expr()->gt('l10n_parent', $qb->createNamedParameter(0)),
                $qb->expr()->eq('deleted', $qb->createNamedParameter(0))
            )
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($tags as $tag) {
            $tagUid       = (int)$tag['uid'];
            $parentUid    = (int)$tag['l10n_parent'];

            // Check if MM rows exist for the translated record (uid_local = tag uid)
            $qbMm = $conn->getQueryBuilderForTable($mmTable);
            $mmRows = $qbMm
                ->select('uid_local', 'uid_foreign', 'sorting', 'sorting_foreign')
                ->from($mmTable)
                ->where($qbMm->expr()->eq('uid_local', $qbMm->createNamedParameter($tagUid)))
                ->executeQuery()
                ->fetchAllAssociative();

            if (empty($mmRows)) {
                continue;
            }

            // Collect already-existing uid_foreign values for the parent (uid_local = l10n_parent)
            $qbExist = $conn->getQueryBuilderForTable($mmTable);
            $existingForeign = $qbExist
                ->select('uid_foreign')
                ->from($mmTable)
                ->where($qbExist->expr()->eq('uid_local', $qbExist->createNamedParameter($parentUid)))
                ->executeQuery()
                ->fetchFirstColumn();

            $existingSet = array_flip($existingForeign);

            foreach ($mmRows as $mmRow) {
                $uidForeign = (int)$mmRow['uid_foreign'];

                if (isset($existingSet[$uidForeign])) {
                    $stats['skipped']++;
                    continue;
                }

                try {
                    $conn->getConnectionForTable($mmTable)->insert($mmTable, [
                        'uid_local'       => $parentUid,
                        'uid_foreign'     => $uidForeign,
                        'sorting'         => (int)$mmRow['sorting'],
                        'sorting_foreign' => (int)($mmRow['sorting_foreign'] ?? 0),
                    ]);
                    $stats['created']++;
                } catch (\Throwable $e) {
                    $stats['errors'][] = "tag={$tagUid} parent={$parentUid} uid_foreign={$uidForeign}: " . $e->getMessage();
                }
            }
        }

        return $stats;
    }

    private function copyMmRelations(
        \TYPO3\CMS\Core\Database\ConnectionPool $conn,
        string $mmTable,
        int $sourceUid,
        int $targetUid,
        array &$stats
    ): void {

    
        // Load MM rows where uid_local = source (language-1 record)
        $qb = $conn->getQueryBuilderForTable($mmTable);
        $rows = $qb
            ->select('uid_local', 'uid_foreign', 'sorting', 'sorting_foreign')
            ->from($mmTable)
            ->where($qb->expr()->eq('uid_local', $qb->createNamedParameter($sourceUid)))
            ->executeQuery()
            ->fetchAllAssociative();

        if (empty($rows)) {
            return;
        }

        // Collect already-existing target MM rows to avoid duplicates
        $qbExist = $conn->getQueryBuilderForTable($mmTable);
        $existingForeign = $qbExist
            ->select('uid_foreign')
            ->from($mmTable)
            ->where($qbExist->expr()->eq('uid_local', $qbExist->createNamedParameter($targetUid)))
            ->executeQuery()
            ->fetchFirstColumn();

        $existingSet = array_flip($existingForeign);

        foreach ($rows as $row) {
            $uidForeign = (int)$row['uid_foreign'];
            if (isset($existingSet[$uidForeign])) {
                $stats['skipped']++;
                continue;
            }

            try {
                $conn->getConnectionForTable($mmTable)->insert($mmTable, [
                    'uid_local'       => $targetUid,
                    'uid_foreign'     => $uidForeign,
                    'sorting'         => (int)$row['sorting'],
                    'sorting_foreign' => (int)($row['sorting_foreign'] ?? 0),
                ]);
                
                $stats['copied']++;
            } catch (\Throwable $e) {
                $stats['errors'][] = "{$mmTable} source={$sourceUid} target={$targetUid} uid_foreign={$uidForeign}: " . $e->getMessage();
            }
        }
    }

    private function buildPaginationItems(int $current, int $total): array
    {
        if ($total <= 1) {
            return [];
        }
        $show = [];
        for ($i = 0; $i < $total; $i++) {
            if ($i === 0 || $i === $total - 1 || abs($i - $current) <= 1) {
                $show[] = $i;
            }
        }
        $items = [];
        $prev = -2;
        foreach ($show as $pagenum) {
            if ($prev !== -2 && $pagenum > $prev + 1) {
                $items[] = -1; // dots sentinel
            }
            $items[] = $pagenum;
            $prev = $pagenum;
        }
        return $items;
    }

    public function indexAction(): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign("insights",$this->insightRepository->findAll());
        $this->view->assign("tags",$this->insighttagRepository->findAll());
        $this->view->assign("categories",$this->insightcategoryRepository->findAll());

        // Merge session filter as base; direct request arguments override
        $sessionFilter = $this->request->getAttribute('frontend.user')->getKey('ses', 'insightFilter') ?? [];
        $this->request->getAttribute('frontend.user')->setKey('ses', 'insightFilter',[]);
        $getArg = function (string $key, mixed $default = null) use ($sessionFilter) {
            if ($this->request->hasArgument($key)) {
                return $this->request->getArgument($key);
            }
            return $sessionFilter[$key] ?? $default;
        };

        $pagesize    = (int) $getArg('pagesize', 12);
        if(!$pagesize){
            $pagesize = 12;
        }
        $page        = (int) $getArg('page', 0);
        $searchquery = $getArg('searchquery', '');
        $categories  = $getArg('categories', []);
        $tags        = $getArg('tags', []); 
        if ($searchquery) {
            $this->view->assign('searchquery', $searchquery);
        }

        $insightsallcount = $this->insightRepository->countAll();
        $this->view->assign('insightsallcount', $insightsallcount);
        $this->view->assign('currentpage', $page);

        

        if (!empty($categories) || !empty($tags)) {
            if (!empty($categories)) {
                $this->view->assign("selectedCategories", array_flip((array)$categories));
            }
            if (!empty($tags)) {
                $this->view->assign("selectedTags", array_flip((array)$tags));
            }
            $this->view->assign('insights', $this->insightRepository->findByAttributes($categories, $tags, $searchquery, $page, $pagesize));
            $insightscount = $this->insightRepository->findByAttributes($categories, $tags, $searchquery);
        } elseif ($searchquery) {
            $this->view->assign('insights', $this->insightRepository->findByAttributes([], [], $searchquery, $page, $pagesize));
            $this->view->assign("selectedCategories", []);
            $insightscount = $this->insightRepository->findByAttributes([], [], $searchquery);
        } else {
            $this->view->assign('insights', $this->insightRepository->findByAttributes([], [], "", $page, $pagesize));
            $this->view->assign("selectedCategories", []);
            $insightscount = $this->insightRepository->findByAttributes([], [], "");
        }
       
        $totalPages = (int)ceil($insightscount / $pagesize);
        $this->view->assign('pages', array_fill(0, $totalPages, 1));
        $this->view->assign('insightscount', $insightscount);
        $this->view->assign('pagesize', $pagesize);
        $this->view->assign('paginationItems', $this->buildPaginationItems($page, $totalPages));
        $this->view->assign('lastPage', max(0, $totalPages - 1));

        return $this->htmlResponse();
    }

    /**
     * @param \Indiz\Products\Domain\Model\Insight $insight
     */	
    public function showAction(\Indiz\Products\Domain\Model\Insight $insight): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign('insight',$insight);
        $insights = $this->insightRepository->findByAttributes([], [], "",0,6);
        $this->view->assign('insights',$insights);
        return $this->htmlResponse();
    }

    /**
     */	
    public function teaseAction(): \Psr\Http\Message\ResponseInterface
    {
        $insights = $this->insightRepository->findByAttributes([], [], "",0,6);
        $this->view->assign('insights',$insights);
        return $this->htmlResponse();
    }

    public function importAction(): \Psr\Http\Message\ResponseInterface
    {
        //$this->syncCategories();
     //  $this->relinkDocuments();
        //    print_r($st);exit;
    exit;
          
        $stats = ['created' => 0, 'errors' => []];

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $fileInfo = $this->resolveUploadedFile('csvFile');

            if (true || $fileInfo && $fileInfo['error'] === \UPLOAD_ERR_OK) {
                $filename = "/var/www/typo3/default/htdocs/vendor/indiz/products/tx_news_domain_model_news.csv";
                $stats = $this->processCsvImport($filename);
                $swapErrors = $this->swapLanguages();
                $stats["errors"] = isset($stats["errors"])?array_merge($stats["errors"],$swapErrors):$swapErrors;
                
            }
        }
        $this->copyImages();

        $this->syncCategories();
        $this->syncTags();

        $this->view->assign('stats', $stats);
        return $this->htmlResponse();
    }

    private function relinkDocuments()
    {
        $conn = GeneralUtility::makeInstance(ConnectionPool::class);
        $stats = ['updated' => 0, 'skipped' => 0, 'errors' => []];

        // Direct PDO connection to the source database
        $sourcePdo = new \PDO(
            'mysql:host=localhost;dbname=sst_typo3;charset=utf8mb4',
            'idi-all',
            'choshieju1ooN5ie',
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );

        // Load bodytext from the source database
        $stmt = $sourcePdo->prepare(
            'SELECT uid, bodytext,title FROM tx_news_domain_model_news WHERE bodytext LIKE ? AND deleted = 0'
        );
        $stmt->execute(['%<link file:%']);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $newsUid  = (int)$row['uid'];
            $original = $row['bodytext'];
                                            echo $row["title"];

            // Pattern: <link file:UID TARGET CLASS TITLE>link text</link>
            $updated = preg_replace_callback(
                '/<link file:(\d+)([^>]*)>(.*?)<\/link>/is',
                function (array $matches) use ($conn, $sourcePdo, &$stats): string {
                    $oldFileUid = (int)$matches[1];
                    $attrs      = trim($matches[2]);
                    $text       = $matches[3];

                    // Look up identifier in source database
                    $fileStmt = $sourcePdo->prepare('SELECT identifier FROM sys_file WHERE uid = ?');
                    $fileStmt->execute([$oldFileUid]);
                    $sourceFile = $fileStmt->fetch(\PDO::FETCH_ASSOC);

                    if (!$sourceFile) {
                        $stats['errors'][] = "sst_typo3 sys_file uid={$oldFileUid} not found — link left unchanged";
                        return $matches[0];
                    }

                    // Find the same file in the current DB by identifier (UIDs may differ)
                     



                                            $filearray = explode("/",$sourceFile['identifier']);
                                            $f_name = $filearray[array_key_last($filearray)];
                                        $dir = $filearray[array_key_last($filearray)-1];
                                        $dir2 = substr($dir,0,4);

                                            $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
                                            $storage = $resourceFactory->getStorageObject(1); // storage UID
                                            
                                            $folder_found = true;
                                            try {
                                                $folder  = $storage->getFolder("www.stepping-stone.ch/insights/" . $dir2 . "/" . $dir);
                                            } catch (FolderDoesNotExistException $e) {
                                                $folder_found = false;
                                                //$errors['errors'][] = "Folder not found: " . $filename;
                                            }
                                            if(!$folder_found){
                                                try {
                                                    $folder  = $storage->getFolder("www.stepping-stone.ch/insights/archive/");
                                                } catch (FolderDoesNotExistException $e) {
                                                    // $errors["filesmissing"][] = "scp /var/www/typo3/app-001/fileadmin_old" . $filename . " /var/www/typo3/app-001/htdocs/public/fileadmin/www.stepping-stone.ch/insights/".$f_name;
                                                    $errors["filesmissing"][] = $filename ;
                                                return false;
                                                }
                                            }
                                            echo "<br>";
                                            echo "searching file ".$f_name." in ".$dir2 ."/".$dir." orig".$sourceFile['identifier'];
                                            
                                            try {
                                                $file = $folder->getFile($f_name);
                                                if(!$file){
                                                    return  $matches[0];
                                                }
                                                $fileUid = $file->getUid();
                                                echo "has found it";
                                            } catch (Throwable $e) {
                                                echo "has not found it";
                                                //$stats['errors'][] = "sys_file identifier={$sourceFile['identifier']} not found in current DB — link left unchanged";
                                                return $matches[0];
                                            }

                    if (!$fileUid) {
                        $stats['errors'][] = "sys_file identifier={$sourceFile['identifier']} not found in current DB — link left unchanged";
                        return $matches[0];
                    }

                    // First token after uid is the target (_blank etc.), '-' means none
                    $attrParts  = preg_split('/\s+/', $attrs, -1, PREG_SPLIT_NO_EMPTY);
                    $targetAttr = '';
                    if (!empty($attrParts[0]) && $attrParts[0] !== '-') {
                        $targetAttr = ' target="' . htmlspecialchars($attrParts[0], ENT_QUOTES) . '"';
                    }

                    return '<a href="t3://file?uid=' . $fileUid . '"' . $targetAttr . '>' . $text . '</a>';
                },
                $original
            );

            if ($updated === $original) {
                $stats['skipped']++;
                continue;
            }

            // Write updated bodytext to the current insight record (matched by uid)
            try {
                $conn->getConnectionForTable('tx_products_domain_model_insight')->update(
                    'tx_products_domain_model_insight',
                    ['bodytext' => $updated],
                    ['uid' => $newsUid]
                );
                $stats['updated']++;
            } catch (\Throwable $e) {
                $stats['errors'][] = "insight uid={$newsUid}: " . $e->getMessage();
            }
        }

        return $stats;
    }
    

    private function swapLanguages(){
        $insights = $this->insightRepository->findAll();
 
        $paired = [];
        $error = [];
        foreach($insights as $insight){
            //if(isset($paired[$insight->getUid])){#
            $insightUid = $insight["l10n_parent"]?:$insight["uid"];
            $paired[$insightUid][] = $insight;
            //}
        }
        
        
        foreach($paired as $insightUid=>$pair){
            if(count($pair) == 1){
                GeneralUtility::makeInstance(ConnectionPool::class)
                    ->getConnectionForTable('tx_products_domain_model_insight')
                    ->update(
                        'tx_products_domain_model_insight',
                        ['sys_language_uid' => -1],
                        ['uid' => $insight["uid"]]
                    );
            }elseif(count($pair) == 2){
                if($pair[0]["sys_language_uid"] == 0){
                    $en = $pair[1];
                    $de = $pair[0];
                }else{
                    $de = $pair[1];
                    $en = $pair[0];
                }
                if($de["uid"] == $en["uid"]){
                    die($en["uid"] .$de["uid"] . "error in finding translations");
                }
                
                GeneralUtility::makeInstance(ConnectionPool::class)
                    ->getConnectionForTable('tx_products_domain_model_insight')
                    ->update(
                        'tx_products_domain_model_insight',
                        ['sys_language_uid' => 1,'l10n_parent'=>$en["uid"]],
                        ['uid' => $de["uid"]]
                    );
                    
                GeneralUtility::makeInstance(ConnectionPool::class)
                    ->getConnectionForTable('tx_products_domain_model_insight')
                    ->update(
                        'tx_products_domain_model_insight',
                        ['sys_language_uid' => 0,'l10n_parent'=>0],
                        ['uid' => $en["uid"]]
                    );
            }else{
                $error[] = "error in translations with count ".($pair[0]["title"]) . " and uid ". $insightUid ." and count" . count($pair);
            }
        }
        return $error;

    }

    private function resolveUploadedFile(string $inputName): ?array
    {
        // Flat key: <input type="file" name="csvFile">
        if (isset($_FILES[$inputName]) && is_string($_FILES[$inputName]['tmp_name'])) {
            return $_FILES[$inputName];
        }

        // Namespaced key: <input type="file" name="tx_products_...[csvFile]">
        foreach ($_FILES as $nsFiles) {
            if (isset($nsFiles['tmp_name'][$inputName])) {
                return [
                    'tmp_name' => $nsFiles['tmp_name'][$inputName],
                    'error'    => $nsFiles['error'][$inputName],
                    'name'     => $nsFiles['name'][$inputName],
                    'size'     => $nsFiles['size'][$inputName],
                ];
            }
        }

        return null;
    }

    private function processCsvImport(string $filePath): array
    {
        $stats    = ['created' => 0, 'errors' => []];
        $storagePid = (int)($this->settings['storagePid'] ?? 61);

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return ['created' => 0, 'errors' => ['Could not open uploaded file']];
        }

        // Strip UTF-8 BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }
        $header = fgetcsv($handle, 0, ",");
        if (!$header) {
            fclose($handle);
            return ['created' => 0, 'errors' => ['File is empty or has no header row']];
        }
        
        $header = array_map('trim', $header);

        // Maps CSV uid/title of default-language records to their new TYPO3 UID,
        // so translated records can resolve l10n_parent correctly.
        $uidMap = [];

        $lineNumber = 1;
        $insights = [];
        while (($row = fgetcsv($handle, 0, ",")) !== false) {
            $data = array_combine($header, array_map('trim', $row));
            
            if(!isset($insights[$data["uid"]])){

                $insights[$data["uid"]] = $data;
                $insights[$data["uid"]]["fal_media"] = [];
                $insights[$data["uid"]]["fal_related_files"] = [];
                $insights[$data["uid"]]["catnames"] = [];
                $insights[$data["uid"]]["tagnames"] = [];
                
            }
            if(in_array($data["fieldName"],["fal_media","fal_related_files"]) ){
                if(!in_array($data["identifier"],$insights[$data["uid"]][$data["fieldName"]])){
                    $insights[$data["uid"]][$data["fieldName"]][] = $data["identifier"];
                }
            }
            if(strlen($data["tagname"]) && !in_array($data["tagname"],$insights[$data["uid"]]["tagnames"])){
                $insights[$data["uid"]]["tagnames"][] = $data["tagname"];
            }
            if(strlen($data["catname"]) && !in_array($data["catname"],$insights[$data["uid"]]["catnames"])){
                $insights[$data["uid"]]["catnames"][] = $data["catname"];
            }
            

        }

        $old_cat = [["Artificial Intelligence","Events"],
                ["AI","Technical article"],
                ["Workshop","Events"],
                ["openDesk","Technical article"],
                ["stoney office","Technical article"],
                ["stepping stone Team","Team"],
                ["OpenSSH","Technical article"],
                ["Terraform","Events"],
                ["Data center","Technical article"],
                ["Anniversary","Events"],
                ["Backup as a Service","Technical article"],
                ["CH Open","Events"],
                ["stoney cloud","Technical article"],
                ["Kubernetes","Technical article"],
                ["stoney backup","Technical article"],
                ["Infrastructure","Technical article"],
                ["CI/CD","Technical article"],
                ["stoney wiki","Technical article"],
                ["Container as a Service","Technical article"],
                ["PaaS","Technical article"],
                ["Platform as a Service","Technical article"],
                ["Software as a Service","Technical article"],
                ["Open source","Technical article"],
                ["SaaS","Technical article"],
                ["ISO/IEC 27001","Technical article"],
                ["Information Security","Technical article"],
                ["Jobs","Team"],
                ["Planned maintenance work","Maintenance Window"],
                ["Disruption","Maintenance Window"],
                ["Maintenance work","Maintenance Window"],
                ["Events","Events"],
                ["Retrospection","Events"],
                ["CEPH","Technical article"],
                ["Cloud computing","Technical article"],
                ["Gallery","Events"],
                ["GlusterFS","Events"],
                ["High availabitity","Events"],
                ["IaaS","Technical article"],
                ["Infrastructure as a Service","Technical article"],
                ["Open Cloud Day","Events"],
                ["Open Source Study","Technical article"],
                ["OpenSSL","Technical article"],
                ["OpenStack","Technical article"],
                ["Partner","Events"],
                ["Products","Technical article"],
                ["Publications","News"],
                ["Customer event","Events"],
                ["Security","Technical article"],
                ["Sponsoring","Events"],
                ["Swiss IT Magazine","News"],
                ["Announcements","News"],
                ["Video","Events"],
                ["Presentation","Events"]];
         
        foreach($insights as $data){
           
            
            $lineNumber++;
            

            try {
                $insight = $this->createInsightFromRow($data, $storagePid);

                /*if (!empty($data['catnames'])) {
                    $objects = $this->resolveOrCreateByName($data['catnames'], $this->insightcategoryRepository, \Indiz\Products\Domain\Model\Insightcategory::class, $storagePid);
                    foreach ($tagobjects as $cat) {
                        $insight->getCategories()->attach($cat);
                    }
                }*/
                //$cats["Artificial Intelligence"]

                $newcats = [];
                if (!empty($data['tagnames'])) {
                    $tagobjects = $this->resolveOrCreateByName($data['tagnames'], $this->insighttagRepository, \Indiz\Products\Domain\Model\Insighttag::class, $storagePid);
                    foreach ($tagobjects as $tag) {
                        $insight->getTags()->attach($tag);
                        foreach($old_cat as $oldcat){
                            if($oldcat[0] == $tag->getName()){
                                $newcats[] = $oldcat[1];
                            }
                        }
                    }
                }
                if (!empty($newcats)) {
                    $objects = $this->resolveOrCreateByName($newcats, $this->insightcategoryRepository, \Indiz\Products\Domain\Model\Insightcategory::class, $storagePid);
                    foreach ($objects as $cat) {
                        $insight->getCategories()->attach($cat);
                    }
                }

                $this->insightRepository->add($insight);
                $this->persistenceManager->persistAll();
                //echo $lineNumber . ":" . $insight->getTitle()."<br>";
                $uid = $insight->getUid();

                

                $this->updateNativeFields($uid, $data, $uidMap);

                $this->addFiles($data['fal_media'], 'fal_media', $uid, $storagePid,$stats);
                $this->addFiles($data['fal_related_files'], 'fal_related_files', $uid, $storagePid,$stats);
                $stats['created']++;
            } catch (\Throwable $e) {
                
                $stats['errors'][] = "Line {$lineNumber}: " .$data["title"]. $e->getMessage();
            }
        }
    
        fclose($handle);
        return $stats;
    }

    private function addFiles($datas,$field,$uid,$storagePid,&$stats){
        if (!empty($datas) && $uid) {
            $fal_medias = array_map('trim',$datas); 
            foreach($fal_medias as $fal_media){
                /*if(in_array($fal_media,$this->imagenames)){
                    $stats['errors'][] = $fal_media . " already used ";
                }*/
                $this->imagenames[] = $fal_media;
                $attached = $this->attachFalReference(
                    $fal_media, $uid, $field, $storagePid,$stats
                );
                if (!$attached) {
                    $stats['errors'][] = "fal_media file '{$fal_media}' not found in storage 1";
                }
            } 
        // Keep the FAL count column in sync
        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_products_domain_model_insight')
            ->update(
                'tx_products_domain_model_insight',
                [$field => count($fal_medias)],
                ['uid' => $uid]
            );
        }
    }

    private function resolveOrCreateByName(array $csvValues, \TYPO3\CMS\Extbase\Persistence\Repository $repository, string $modelClass, int $pid): array
    {
        $objects = [];
        foreach (array_filter(array_map('trim', $csvValues)) as $name) {
            $object = $repository->findOneByName($name);
            if (!$object) {
                $object = new $modelClass();
                $object->setName($name);
                $object->setPid($pid);
                $repository->add($object);
            }
            $objects[] = $object;
        }
        return $objects;
    }

    private function createInsightFromRow(array $data, int $pid): Insight
    {
        $insight = new Insight();
        $insight->setPid($pid);
        $insight->setTitle($data['title'] ?? '');
        $insight->setHidden(trim($data['hidden']) == "No" ?0:1);
        $insight->setAlternativeTitle($data['alternative_title'] ?? '');
        $insight->setTeaser($data['teaser'] ?? '');
        $insight->setBodytext($data['bodytext'] ?? '');
        $insight->setAuthor($data['author'] ?? '');
        $insight->setAuthorEmail($data['author_email'] ?? '');
        $insight->setType((string)($data['type'] ?? '0'));
        $insight->setKeywords($data['keywords'] ?? '');
        $insight->setDescription($data['description'] ?? '');
        $insight->setInternalurl($data['internalurl'] ?? '');
        $insight->setExternalurl($data['externalurl'] ?? '');
        $insight->setIstopnews(($data['istopnews'] ?? '0') === '1');
        $insight->setPathSegment($data['path_segment'] ?? '');
        
        // CSV header is 'note' but model field is 'notes'
        $insight->setNotes($data['notes'] ?? ($data['note'] ?? ''));

        foreach (['datetime', 'archive'] as $dateField) {
            $dt = $data[$dateField];
            if ($dt !== null && $dt !== '') {
                $setter = 'set' . ucfirst($dateField);
                $insight->$setter(new \DateTime('@' . (int)$dt));
            }
        }

        return $insight;
    }

    private function updateNativeFields(int &$uid, array $data, array $uidMap = []): void
    {
        $fields = [];

        $intFields = ['sys_language_uid', 'hidden', 'editlock',"l10n_parent","uid"];
        foreach ($intFields as $field) {
            if (isset($data[$field]) && $data[$field] !== '') {
                $fields[$field] = (int)$data[$field];
            }
        }

       
       
        if (!empty($data['fe_group'])) {
            $fields['fe_group'] = $data['fe_group'];
        }

        foreach (['starttime', 'endtime'] as $field) {
            if (!empty($data[$field])) {
                $ts = strtotime($data[$field]);
                if ($ts !== false) {
                    $fields[$field] = $ts;
                }
            }
        }

        if ($fields) {
            GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable('tx_products_domain_model_insight')
                ->update('tx_products_domain_model_insight', $fields, ['uid' => $uid]);
        }
        $uid = (int)$data["uid"];
    }

    private function attachFalReference(string $filename, int $recordUid, string $fieldName, int $pid,array &$errors): bool
    {
        $filename = trim($filename);
        
        
        if ($filename === '') {
            return false;
        }
        
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_file');

        
        $filearray = explode("/",$filename);
        $f_name = $filearray[array_key_last($filearray)];
       $dir = $filearray[array_key_last($filearray)-1];
       $dir2 = substr($dir,0,4);

        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
        $storage = $resourceFactory->getStorageObject(1); // storage UID
        
        $folder_found = true;
        try {
            $folder  = $storage->getFolder("www.stepping-stone.ch/insights/" . $dir2 . "/" . $dir);
        } catch (FolderDoesNotExistException $e) {
            $folder_found = false;
            //$errors['errors'][] = "Folder not found: " . $filename;
        }
        if(!$folder_found){
            try {
                $folder  = $storage->getFolder("www.stepping-stone.ch/insights/archive/");
            } catch (FolderDoesNotExistException $e) {
                // $errors["filesmissing"][] = "scp /var/www/typo3/app-001/fileadmin_old" . $filename . " /var/www/typo3/app-001/htdocs/public/fileadmin/www.stepping-stone.ch/insights/".$f_name;
                $errors["filesmissing"][] = $filename ;
            return false;
            }
        }

        
        try {
            $file = $folder->getFile($f_name);
             $fileUid = $file->getUid();
        } catch (FileDoesNotExistException $e) {

            $errors['errors'][] = "file not found: " . $filename;
        }

        $refConnection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('sys_file_reference');

        $refConnection->insert('sys_file_reference', [
            'tstamp'          => time(),
            'crdate'          => time(),
            'uid_local'       => $fileUid,
            'uid_foreign'     => $recordUid,
            'tablenames'      => 'tx_products_domain_model_insight',
            'fieldname'       => $fieldName,
            'pid'             => $pid,
            'l10n_parent'     => 0,
            'sorting_foreign' => 0,
        ]);

        

            //$errors['errors'][] = "file found: " . $filename;

       

        return true;
    }
}
