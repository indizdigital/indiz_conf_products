<?php

namespace Indiz\Products\Domain\Model;

class Packageelement extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    protected string $name = '';
    protected int $amount = 0;

    protected ?Productelement $productelement = null;

    // Getter / Setter
	public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
    
    public function getProductelement(): Productelement
    {
        return $this->productelement;
    }

    public function setProductelement($productelement): Productelement
    {
        $this->productelement = $productelement;
    }
public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

}
