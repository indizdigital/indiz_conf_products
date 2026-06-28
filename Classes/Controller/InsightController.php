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
        $feUser->setKey('ses', 'productFilter', $filter);
        $feUser->storeSessionData();

        return $this->redirect('index');
    }

    public function indexAction(): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign("insights",$this->insightRepository->findAll());
        $this->view->assign("tags",$this->insighttagRepository->findAll());
        $this->view->assign("categories",$this->insightcategoryRepository->findAll());

        // Merge session filter as base; direct request arguments override
        $sessionFilter = $this->request->getAttribute('frontend.user')->getKey('ses', 'productFilter') ?? [];
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

        // Singular 'category' comes from the URL route enhancer; normalise into the array used everywhere else
        if ($this->request->hasArgument("category") && !empty($this->request->getArgument("category"))) {
            $categories = [(int)$this->request->getArgument("category")];
            $this->view->assign("selectedCategories", array_flip($categories));
            $this->view->assign('insights', $this->insightRepository->findByAttributes($categories, [], $searchquery, $page, $pagesize));
            $this->view->assign('insightscount', $this->insightRepository->findByAttributes($categories, [], $searchquery));
            return $this->htmlResponse();
        }

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
       
        $this->view->assign('pages', array_fill(0, (int) ceil($insightscount / $pagesize), 1));
        $this->view->assign('insightscount', $insightscount);
        $this->view->assign('pagesize', $pagesize);

        
        return $this->htmlResponse();
    }

    /**
     * @param \Indiz\Products\Domain\Model\Insight $insight
     */	
    public function showAction(\Indiz\Products\Domain\Model\Insight $insight): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign('insight',$insight);
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

        $this->view->assign('stats', $stats);
        return $this->htmlResponse();
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
        
        
        foreach($insights as $data){
            $lineNumber++;
            

            try {
                $insight = $this->createInsightFromRow($data, $storagePid);

                if (!empty($data['catnames'])) {
                    foreach ($this->resolveOrCreateByName($data['catnames'], $this->insightcategoryRepository, \Indiz\Products\Domain\Model\Insightcategory::class, $storagePid) as $cat) {
                        $insight->getCategories()->attach($cat);
                    }
                }

                if (!empty($data['tagnames'])) {
                    foreach ($this->resolveOrCreateByName($data['tagnames'], $this->insighttagRepository, \Indiz\Products\Domain\Model\Insighttag::class, $storagePid) as $tag) {
                        $insight->getTags()->attach($tag);
                    }
                }

                $this->insightRepository->add($insight);
                $this->persistenceManager->persistAll();
                //echo $lineNumber . ":" . $insight->getTitle()."<br>";
                $uid = $insight->getUid();

                // Track by title so translated records can resolve l10n_parent by title
                if (!empty($data['title'])) {
                    $uidMap[$uid] = $data['title'];
                }

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

    private function updateNativeFields(int $uid, array $data, array $uidMap = []): void
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

       

        return true;
    }
}
