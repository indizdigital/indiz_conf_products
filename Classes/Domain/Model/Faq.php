<?php

namespace Indiz\Products\Domain\Model;

class Faq extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    protected string $name = '';
    protected string $description = '';
    // Getter / Setter
    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }
    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription($description): void
    {
        $this->description = $description;
    }
}
