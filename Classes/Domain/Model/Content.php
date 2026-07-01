<?php
namespace Indiz\Products\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Content extends AbstractEntity
{
    protected string $bodytext = '';
    protected string $header = '';
    protected int $defaultopen = 0;

    public function getBodytext(): string
    {
        return $this->bodytext;
    }

    public function setBodytext(string $bodytext): void
    {
        $this->bodytext = $bodytext;
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function setHeader(string $header): void
    {
        $this->header = $header;
    }

    public function getDefaultopen(): int
    {
        return $this->defaultopen;
    }

    public function setDefaultopen(int $defaultopen): void
    {
        $this->defaultopen = $defaultopen;
    }
}
