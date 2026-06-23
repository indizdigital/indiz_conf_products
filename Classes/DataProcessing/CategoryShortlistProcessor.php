<?php

declare(strict_types=1);

namespace Indiz\Products\DataProcessing;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Core\Context\Context;

class CategoryShortlistProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $targetVariable = $processorConfiguration['as'] ?? 'categories';

        /** @var ServerRequestInterface $request */
        $request = $GLOBALS['TYPO3_REQUEST'];
        $selectedCategories = [];
        if(isset($request->getQueryParams()['tx_products_index']['categories'])){
            $selectedCategories = ($request->getQueryParams()['tx_products_index']['categories'] ?? []);
        }
        if(empty($selectedCategories) && isset($request->getParsedBody()['tx_products_index']['categories'])){
            $selectedCategories = $request->getParsedBody()['tx_products_index']['categories'] ?? [];
        }
        $selectedCategory = is_array($selectedCategories) && count($selectedCategories)?array_shift($selectedCategories):0;
        
        $table = 'tx_products_domain_model_category';

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table);

        $languageId = GeneralUtility::makeInstance(Context::class)
            ->getPropertyFromAspect('language', 'id', 0);

        $conditions = [
            $queryBuilder->expr()->or(
                 $queryBuilder->expr()->eq( $table . '.sys_language_uid',$queryBuilder->createNamedParameter($languageId) ),
                 $queryBuilder->expr()->eq( $table . '.sys_language_uid',-1 )
            ),
            $queryBuilder->expr()->eq('show_in_menu', $queryBuilder->createNamedParameter('1'))
        ];


        $categories = $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                ...$conditions
            )
            ->orderBy("sorting")
            ->executeQuery()
            ->fetchAllAssociative();
        
        if($selectedCategory){
            foreach($categories as &$cat){
                if($cat["uid"] == $selectedCategory || $cat["l10n_parent"] == $selectedCategory){
                    $cat["active"] = 1;
                }
            }
        }
        $processedData[$targetVariable] = $categories;
        
        return $processedData;
    }
}
