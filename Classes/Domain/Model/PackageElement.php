<?php

namespace \Indiz\Products\Domain\Model;

class PackageElement extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    protected string $name = '';
    protected int $amount = 0;
    protected bool $isBase = false;

    protected ?ProductElement $productelement = null;

    // Getter / Setter
}