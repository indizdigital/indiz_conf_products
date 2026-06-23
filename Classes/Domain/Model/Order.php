<?php

namespace Indiz\Products\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use Indiz\Products\Domain\Model\ProductelementRepository;

class Order extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    protected string $ordername = '';
    protected int $ordertype = 0;
    protected string $name = '';
    protected string $firstname = '';
    protected string $email = '';
    protected string $addressline = '';
    protected string $addressline2 = '';
    protected string $postalcode = '';
    protected string $city = '';
    protected string $country = '';
    protected string $company = '';
    protected string $phone = '';
    protected string $data = '';
    protected float $total = 0.0;
    protected int $packageUid = 0;
    protected int $productUid = 0;
    protected string $gender = '';
    protected ?int $agb = 0;
    protected ?int $newsletter = 0;

    // Getter / Setter
    public function getOrdername(): string
    {
        return $this->ordername;
    }

    public function setOrdername(string $ordername): void
    {
        $this->ordername = $ordername;
    }
    public function getOrdertype(): int
    {
        return $this->ordertype;
    }  
    public function setOrdertype(int $ordertype): void
    {
        $this->ordertype = $ordertype;
    }
    public function getData(): string
    {
        return $this->data;
    }

    public function setData(string $data): void
    {
        $this->data = $data;
    }
    // Getter / Setter
    public function getName(): string
    {
        return $this->name;
    }

    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }
    public function getFirstname(): string
    {
        return $this->firstname;
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

    public function getAddressline(): string
    {
        return $this->addressline;
    }

    public function setAddressline(string $addressline): void
    {
        $this->addressline = $addressline;
    }

    public function getAddressline2(): string
    {
        return $this->addressline2;
    }

    public function setAddressline2(string $addressline2): void
    {
        $this->addressline2 = $addressline2;
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

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
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

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    public function getGender(): string
    {
        return $this->gender;
    }
    public function setGender($gender): void
    {
        $this->gender = $gender;
    }

    public function getAgb(): int
    {
        return $this->agb ?? 0;
    }
    public function setAgb($agb = 0): void
    {
        $this->agb = $agb;
    }

    public function getNewsletter(): int
    {
        return $this->newsletter ?? 0;
    }
    public function setNewsletter($newsletter = 0): void
    {
        $this->newsletter = $newsletter;
    }
}
