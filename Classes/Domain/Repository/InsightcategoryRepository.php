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

    /**
     * Translated records only get their `sorting` copied from the default
     * language record once, at localization time - reordering the default
     * language afterwards does not propagate to translations. This resolves
     * every record's effective sorting via its default-language counterpart
     * so translated categories stay in the same order as language 0.
     *
     * @return \Indiz\Products\Domain\Model\Insightcategory[]
     */
    public function findAllOrderedByDefaultLanguage(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->table);
        $queryBuilder->getRestrictions()->removeAll();

        $rows = $queryBuilder
            ->select('uid', 'l10n_parent', 'sorting')
            ->from($this->table)
            ->executeQuery()
            ->fetchAllAssociative();

        $defaultSortingByUid = [];
        foreach ($rows as $row) {
            if ((int)$row['l10n_parent'] === 0) {
                $defaultSortingByUid[(int)$row['uid']] = (int)$row['sorting'];
            }
        }

        $effectiveSortingByUid = [];
        foreach ($rows as $row) {
            $uid = (int)$row['uid'];
            $parent = (int)$row['l10n_parent'];
            $effectiveSortingByUid[$uid] = $defaultSortingByUid[$parent] ?? (int)$row['sorting'];
        }

        $categories = $this->findAll()->toArray();
        usort(
            $categories,
            fn($a, $b) => ($effectiveSortingByUid[$a->getUid()] ?? 0) <=> ($effectiveSortingByUid[$b->getUid()] ?? 0)
        );

        return $categories;
    }

}
