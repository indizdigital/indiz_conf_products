<?php

namespace Indiz\Products\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class InsightcategoryRepository extends Repository
{
    protected $table = 'tx_products_domain_model_insightcategory';
    
    protected $defaultOrderings = [
        'sorting'   => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
    ];

    // You can add custom query methods here later

    public function initializeObject(): void
    {
        $querySettings = $this->createQuery()->getQuerySettings();
        // Show comments from all pages
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }
    
}
