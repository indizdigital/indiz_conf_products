<?php

declare(strict_types=1);

namespace Indiz\Products\DataProcessing;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

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
        

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_products_domain_model_category');


        $categories = $queryBuilder
            ->select('*')
            ->from('tx_products_domain_model_category')
            ->where(
                $queryBuilder->expr()->eq('show_in_menu', $queryBuilder->createNamedParameter('1'))
            )
            ->executeQuery()
            ->fetchAllAssociative();
        foreach($categories as &$cat){
            if($cat["uid"] == $selectedCategory){
                $cat["active"] = 1;
            }
        }
        $processedData[$targetVariable] = $categories;
        
        return $processedData;
    }
}
