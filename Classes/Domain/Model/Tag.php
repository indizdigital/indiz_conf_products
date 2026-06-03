<?php

namespace Indiz\Products\Domain\Model;

class Tag extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    protected string $name = '';
    // Getter / Setter
    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }
}
