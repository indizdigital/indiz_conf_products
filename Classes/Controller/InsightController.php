<?php

declare(strict_types=1);

namespace Indiz\Products\Controller;

use Doctrine\DBAL\ParameterType;
use Indiz\Products\Domain\Model\Insight;
use Indiz\Products\Domain\Repository\InsightRepository;
use Indiz\Products\Domain\Repository\InsighttagRepository;
use Indiz\Products\Domain\Repository\InsightcategoryRepository;
use Indiz\Products\Services\Mailer;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;


class InsightController extends ActionController
{
    public function __construct(
        protected readonly InsightRepository $insightRepository,
        protected readonly InsighttagRepository $insighttagRepository,
        protected readonly InsightcategoryRepository $insightcategoryRepository,
        private readonly PersistenceManager $persistenceManager
    ) {}

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
        $this->view->assign("tags",$this->tagRepository->findAll());

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

        $productsallcount = $this->productRepository->countAll();
        $this->view->assign('productsallcount', $productsallcount);
        $this->view->assign('currentpage', $page);

        // Singular 'category' comes from the URL route enhancer; normalise into the array used everywhere else
        if ($this->request->hasArgument("category") && !empty($this->request->getArgument("category"))) {
            $categories = [(int)$this->request->getArgument("category")];
            $this->view->assign("selectedCategories", array_flip($categories));
            $this->view->assign('products', $this->productRepository->findByAttributes($categories, [], $searchquery, $page, $pagesize));
            $this->view->assign('productscount', $this->productRepository->findByAttributes($categories, [], $searchquery));
            return $this->htmlResponse();
        }

        if (!empty($categories) || !empty($tags)) {
            if (!empty($categories)) {
                $this->view->assign("selectedCategories", array_flip((array)$categories));
            }
            if (!empty($tags)) {
                $this->view->assign("selectedTags", array_flip((array)$tags));
            }
            $this->view->assign('products', $this->productRepository->findByAttributes($categories, $tags, $searchquery, $page, $pagesize));
            $productscount = $this->productRepository->findByAttributes($categories, $tags, $searchquery);
        } elseif ($searchquery) {
            $this->view->assign('products', $this->productRepository->findByAttributes([], [], $searchquery, $page, $pagesize));
            $this->view->assign("selectedCategories", []);
            $productscount = $this->productRepository->findByAttributes([], [], $searchquery);
        } else {
            $this->view->assign('products', $this->productRepository->findByAttributes([], [], "", $page, $pagesize));
            $this->view->assign("selectedCategories", []);
            $productscount = $this->productRepository->findByAttributes([], [], "");
        }

        $this->view->assign('pages', array_fill(0, ceil($productscount / $pagesize), 1));
        $this->view->assign('productscount', $productscount);
        $this->view->assign('pagesize', $pagesize);

        return $this->htmlResponse();
    }

    /**
     * @param \Indiz\Products\Domain\Model\Product $product
     */	
    public function showAction(\Indiz\Products\Domain\Model\Product $product): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign('product',$product);
        return $this->htmlResponse();
    }

    public function importAction(): \Psr\Http\Message\ResponseInterface
    {
        $stats = ['created' => 0, 'errors' => []];

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $fileInfo = $this->resolveUploadedFile('csvFile');

            if ($fileInfo && $fileInfo['error'] === \UPLOAD_ERR_OK) {
                $stats = $this->processCsvImport($fileInfo['tmp_name']);
            }
        }

        $this->view->assign('stats', $stats);
        return $this->htmlResponse();
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

        $lineNumber = 1;
        while (($row = fgetcsv($handle, 0, ",")) !== false) {
            $lineNumber++;
            if (count($row) !== count($header)) {
                $stats['errors'][] = "Line {$lineNumber}: column count mismatch";
                continue;
            }
            $data = array_combine($header, array_map('trim', $row));

            try {
                
                $insight = $this->createInsightFromRow($data, $storagePid);
                if (!empty($data['categories'])) {
                    foreach ($this->resolveOrCreateByName($data['categories'], $this->insightcategoryRepository, \Indiz\Products\Domain\Model\Insightcategory::class, $storagePid) as $cat) {
                        $insight->getCategories()->attach($cat);
                    }
                }

                if (!empty($data['tags'])) {
                    foreach ($this->resolveOrCreateByName($data['tags'], $this->insighttagRepository, \Indiz\Products\Domain\Model\Insighttag::class, $storagePid) as $tag) {
                        $insight->getTags()->attach($tag);
                    }
                }

                $this->insightRepository->add($insight);
                $this->persistenceManager->persistAll();

                $uid = $insight->getUid();
                $this->updateNativeFields($uid, $data);

                if (!empty($data['fal_media']) && $uid) {
                    $attached = $this->attachFalReference(
                        $data['fal_media'], $uid, 'fal_media', $storagePid
                    );
                    if (!$attached) {
                        $stats['errors'][] = "Line {$lineNumber}: fal_media file '{$data['fal_media']}' not found in storage 1";
                    }
                }

                $stats['created']++;
            } catch (\Throwable $e) {
                $stats['errors'][] = "Line {$lineNumber}: " . $e->getMessage();
            }
        }

        fclose($handle);
        return $stats;
    }

    private function resolveOrCreateByName(string $csvValue, \TYPO3\CMS\Extbase\Persistence\Repository $repository, string $modelClass, int $pid): array
    {
        $objects = [];
        foreach (array_filter(array_map('trim', explode(';', $csvValue))) as $name) {
            $object = $repository->findOneByName($name);
            if (!$object) {
                $object = new $modelClass();
                $object->setName($name);
                $object->setPid($pid);
                $repository->add($object);
                $this->persistenceManager->persistAll();
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
            if (!empty($data[$dateField])) {
                $ts = strtotime($data[$dateField]);
                if ($ts !== false) {
                    $dt = new \DateTime();
                    $dt->setTimestamp($ts);
                    $setter = 'set' . ucfirst($dateField);
                    $insight->$setter($dt);
                }
            }
        }

        return $insight;
    }

    private function updateNativeFields(int $uid, array $data): void
    {
        $fields = [];

        $intFields = ['sys_language_uid', 'l10n_parent', 'hidden', 'editlock'];
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

    private function attachFalReference(string $filename, int $recordUid, string $fieldName, int $pid): bool
    {
        $filename = trim($filename);
        if ($filename === '') {
            return false;
        }

        $qb = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_file');

        $fileRow = $qb
            ->select('uid')
            ->from('sys_file')
            ->where(
                $qb->expr()->eq('storage', $qb->createNamedParameter(1, ParameterType::INTEGER)),
                $qb->expr()->eq('name',    $qb->createNamedParameter($filename))
            )
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if (!$fileRow) {
            return false;
        }

        $refConnection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('sys_file_reference');

        $refConnection->insert('sys_file_reference', [
            'tstamp'          => time(),
            'crdate'          => time(),
            'uid_local'       => (int)$fileRow['uid'],
            'uid_foreign'     => $recordUid,
            'tablenames'      => 'tx_products_domain_model_insight',
            'fieldname'       => $fieldName,
            'pid'             => $pid,
            'table_local'     => 'sys_file',
            'l10n_parent'     => 0,
            'sorting_foreign' => 0,
        ]);

        // Keep the FAL count column in sync
        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_products_domain_model_insight')
            ->update(
                'tx_products_domain_model_insight',
                [$fieldName => 1],
                ['uid' => $recordUid]
            );

        return true;
    }
}
