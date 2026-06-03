<?php
declare(strict_types=1);

namespace Indiz\Products\FormDataProvider;

use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PackageElementInitializer implements FormDataProviderInterface
{
    public function addData(array $result): array
    {
       /*
        if ($result['tableName'] === 'tx_products_domain_model_packageelement' && $result['command'] == "new") {
            // Get the parent product UID (for inline records)
            $parentUid = $result["databaseRow"]["uid"];
            echo "Parent UID: " . $parentUid; // Debugging output
            exit;
            if ($parentUid) {
                // Query the count of selected productelements from the parent product
                $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                    ->getQueryBuilderForTable('tx_products_product_productelement_mm');
                $count = $queryBuilder
                    ->count('uid_local')
                    ->from('tx_products_product_productelement_mm')
                    ->where($queryBuilder->expr()->eq('uid_local', $queryBuilder->createNamedParameter($parentUid)))
                    ->executeQuery()
                    ->fetchOne();
                    
                if ($count > 0) {
                    // Set minitems and maxitems to the count (prevents adding more, initializes exactly that many)
                    $result['processedTca']['columns']['packageelements']['config']['minitems'] = (int)$count;
                    $result['processedTca']['columns']['packageelements']['config']['maxitems'] = (int)$count;
                }
            }
        }
    /*if ($result['tableName'] === 'tx_products_domain_model_product') {
            // Get the parent product UID (for inline records)
            $parentUid = $result["databaseRow"]["uid"];
            if ($parentUid) {
                // Query the count of selected productelements from the parent product
                $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                    ->getQueryBuilderForTable('tx_products_product_productelement_mm');
                $count = $queryBuilder
                    ->count('uid_local')
                    ->from('tx_products_product_productelement_mm')
                    ->where($queryBuilder->expr()->eq('uid_local', $queryBuilder->createNamedParameter($parentUid)))
                    ->executeQuery()
                    ->fetchOne();
                    
                if ($count > 0) {
                    // Set minitems and maxitems to the count (prevents adding more, initializes exactly that many)
                    $result['processedTca']['columns']['packageelements']['config']['minitems'] = (int)$count;
                    $result['processedTca']['columns']['packageelements']['config']['maxitems'] = (int)$count;
                }
            }
        }*/

        return $result;
    }
}