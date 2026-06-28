<?php

namespace Indiz\Products\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Indiz\Products\Domain\Model\Product;

class ProductRepository extends Repository
{
    protected $table = 'tx_products_domain_model_product';
    // You can add custom query methods here later

    public function initializeObject(): void
    {
        $querySettings = $this->createQuery()->getQuerySettings();
        $querySettings->setRespectStoragePage(false);
        $querySettings->setRespectSysLanguage(true);
        $this->setDefaultQuerySettings($querySettings);
    }

    public function countAll(): int
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);
        $conditions = $this->getLanguageConditions($queryBuilder);
        return (int)$queryBuilder
            ->count('uid')
            ->from($this->table)
            ->where(...$conditions)
            ->executeQuery()
            ->fetchOne();
    }

    public function getLanguageConditions($queryBuilder){
         $languageId = GeneralUtility::makeInstance(Context::class)
            ->getPropertyFromAspect('language', 'id', 0);

        $conditions = [
            $queryBuilder->expr()->or(
                 $queryBuilder->expr()->eq( $this->table . '.sys_language_uid',$queryBuilder->createNamedParameter($languageId) ),
                 $queryBuilder->expr()->eq( $this->table . '.sys_language_uid',-1 )
            ),
        ];
        return $conditions;
    }

    public function findByAttributes($categories,$tags, $searchquery,$page = 0,$pagesize = 5000)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);
        $mmtable_cats = 'tx_products_product_category';
        $mmtable_tags = 'tx_products_product_tag';
        $query = $queryBuilder
            ->select('*')
            ->from($this->table)
            ->leftJoin(
                'tx_products_domain_model_product',
                $mmtable_cats,
                $mmtable_cats,
                $queryBuilder->expr()->eq($mmtable_cats . '.uid_local', $queryBuilder->quoteIdentifier('tx_products_domain_model_product.uid'))
            
            )
            ->leftJoin(
                'tx_products_domain_model_product',
                $mmtable_tags,
                $mmtable_tags,
                $queryBuilder->expr()->eq($mmtable_tags . '.uid_local', $queryBuilder->quoteIdentifier('tx_products_domain_model_product.uid'))
            
            );

        
        $conditions = $this->getLanguageConditions($query);

        if (!empty($categories)) {
            $i_conditions = [];

            foreach($categories as $category){
                $i_conditions[] = $queryBuilder->expr()->eq($mmtable_cats . '.uid_foreign', $queryBuilder->createNamedParameter($category));
            }
            $conditions[] = $queryBuilder->expr()->or(
                ...$i_conditions
            );
        }
        if (!empty($tags)) {
            $i_conditions = [];

            foreach($tags as $tag){
                $i_conditions[] = $queryBuilder->expr()->eq($mmtable_tags . '.uid_foreign', $queryBuilder->createNamedParameter($tag));
            }
            $conditions[] = $queryBuilder->expr()->or(
                ...$i_conditions
            );
        }

        if ($searchquery) {
            $conditions[] = $queryBuilder->expr()->or(
                $queryBuilder->expr()->like('name', $queryBuilder->createNamedParameter('%' . $searchquery . '%')),
                $queryBuilder->expr()->like('subname', $queryBuilder->createNamedParameter('%' . $searchquery . '%')),
                $queryBuilder->expr()->like('description', $queryBuilder->createNamedParameter('%' . $searchquery . '%'))
            );
        }
        $query->where(...$conditions);
        $query->setMaxResults($pagesize);
        $query->setFirstResult($pagesize * $page);
        
        $query->groupBy($this->table . '.uid');
        $query->orderBy('name', 'ASC');

        if($pagesize == 5000){
            return count($queryBuilder->executeQuery()->fetchAllAssociative());
        }

        $dataMapper = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper::class);
        //print_r($query->executeQuery()->fetchAllAssociative());exit;
        $result = $dataMapper->map(
            Product::class,
            $queryBuilder->executeQuery()->fetchAllAssociative()
        );
        return $result;
        
    }
    
}
