<?php
namespace Indiz\Products\Domain\Model;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use Indiz\Products\Services\Formula;

class Package extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    protected string $name = '';
    protected string $desc = '';
    protected bool $configurable = false;

    protected string $packageelements = "";

    // Getter / Setter
	public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
	public function getDesc(): string
    {
        return $this->desc;
    }

    public function setDesc(string $desc): void
    {
        $this->desc = $desc;
    }
	public function getConfigurable(): bool
    {
        return $this->configurable;
    }

    public function setConfigurable(bool $configurable): void
    {
        $this->configurable = $configurable;
    }
    /*public function getMagicdivs(){
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable('tx_products_domain_model_product');

        $rows = $queryBuilder
        ->select('*')
        ->from('tx_products_domain_model_product')
        ->where(
            $queryBuilder->expr()->eq('name', $queryBuilder->createNamedParameter($this->name))
        )
        ->setMaxResults(1)
        ->executeQuery()
        ->fetchAllAssociative();


        $objects = GeneralUtility::makeInstance(DataMapper::class)->map(Product::class, $rows);
        
        return array_shift($objects);
    }*/
    
    public function getPackageelements()
    {
         $sorted = new ObjectStorage();
         
        if (strlen($this->packageelements) === 0) {
            return $sorted;
        }

        $uids = array_map('intval', array_filter(explode(',', $this->packageelements)));

        if (empty($uids)) {
            return $sorted;
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_products_domain_model_packageelement');

        $rows = $queryBuilder
            ->select('*')
            ->from('tx_products_domain_model_packageelement')
            ->where(
                $queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter($uids, Connection::PARAM_INT_ARRAY))
            )
            ->executeQuery()
            ->fetchAllAssociative();

        $objects = GeneralUtility::makeInstance(DataMapper::class)->map(Packageelement::class, $rows);

        $byUid = [];
        foreach ($objects as $div) {
            $byUid[$div->getUid()] = $div;
        }

        foreach ($uids as $uid) {
            if (isset($byUid[$uid])) {
                $sorted->attach($byUid[$uid]);
            }
        }
        return $sorted;

    }

    public function setPackageelements($packageelements): void
    {
        $this->packageelements = $packageelements;
    }

    public function getTotal(): float
    {
        $formulaservice = GeneralUtility::makeInstance(Formula::class);
        $total = 0.0;
        foreach ($this->getPackageelements() as $packageelement) {
            $price = $packageelement->getTotalPrice();
            $total += $price;
        }
        return $total;
    }

    public function getMinTotal(): float
    {
        $formulaservice = GeneralUtility::makeInstance(Formula::class);
        $total = 0.0;
        foreach ($this->getPackageelements() as $packageelement) {
            $price = $packageelement->getPrice();
            
            $amount = $packageelement->getAmount()?:$packageelement->getMin();
            if($amount){
                $total += $amount * $price;
            }
        }
        return $total;
    }
}
