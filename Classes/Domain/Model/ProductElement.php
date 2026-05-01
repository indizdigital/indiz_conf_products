<?php

namespace \Indiz\Products\Domain\Model;

class ProductElement extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    protected string $name = '';
    protected float $price = 0.0;
    protected int $min = 0;
    protected int $max = 0;

    // Getter / Setter
}