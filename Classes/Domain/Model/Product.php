<?php

namespace \Indiz\Products\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Product extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    protected string $name = '';
    protected string $description = '';
    protected $image = null;

    /**
     * @var ObjectStorage<Package>
     */
    protected $packages;

    public function __construct()
    {
        $this->packages = new ObjectStorage();
    }

    // Getter / Setter
}