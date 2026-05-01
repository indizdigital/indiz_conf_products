<?php

namespace \Indiz\Products\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Package extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    protected string $name = '';

    /**
     * @var ObjectStorage<PackageElement>
     */
    protected $packageelements;

    public function __construct()
    {
        $this->packageelements = new ObjectStorage();
    }

    // Getter / Setter
}