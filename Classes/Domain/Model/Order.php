<?php

namespace Indiz\Products\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;

class Order extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    protected string $ordername = '';
    protected string $name = '';
    protected string $email = '';
    protected string $street = '';
    protected string $postalcode = '';
    protected string $city = '';
    protected string $country = '';
    protected string $company = '';
    protected int $packageUid = 0;
    protected int $productUid = 0;

    // Getter / Setter
    public function getOrdername(): string
    {
        return $this->ordername;
    }

    public function setOrdername(string $ordername): void
    {
        $this->ordername = $ordername;
    }
    // Getter / Setter
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    public function getPostalcode(): string
    {
        return $this->postalcode;
    }

    public function setPostalcode(string $postalcode): void
    {
        $this->postalcode = $postalcode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

    public function getPackageUid(): int
    {
        return $this->packageUid;
    }

    public function setPackageUid(int $packageUid): void
    {
        $this->packageUid = $packageUid;
    }

    public function getProductUid(): int
    {
        return $this->productUid;
    }

    public function setProductUid(int $productUid): void
    {
        $this->productUid = $productUid;
    }
}
