<?php

namespace Indiz\Products\Domain\Model;

class Productelement extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    protected string $name = '';
    protected float $price = 0.0;
	protected string $unit = "";
    protected int $min = 0;
    protected int $max = 0;

    // Getter / Setter
public function getName(): string
{
    return $this->name;
}

public function setName($name): void
{
    $this->name = $name;
}
public function getUnit(): string
{
    return $this->unit;
}

public function setUnit($unit): void
{
    $this->unit = $unit;
}
public function getPrice(): float
{
    return $this->price;
}

public function setPrice($price): void
{
    $this->price = $price;
}

public function getMax(): int
{
    return $this->max;
}

public function setMax($max): void
{
    $this->max = $max;
}

public function setMin($min): void
{
    $this->min = $min;
}
public function getMin(): int
{
    return $this->min;
}

}
