<?php

namespace Indiz\Products\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use Indiz\Products\Domain\Model\Category;
use Indiz\Products\Domain\Model\Content;

class Product extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    protected string $name = '';
    protected string $subname = '';
    protected string $rendertype = '';
    /**
     * @var ObjectStorage<Category>
     */
    protected $categories = null;
    /**
     * @var ObjectStorage<Content>
     */
    protected $leftContent = null;
    /**
     * @var ObjectStorage<Content>
     */
    protected $aiContent = null;
    protected string $shortdescription = '';
    protected string $description = '';
    
    /**
     * @var FileReference
     */
    protected $image = null;
    /**
     * @var ObjectStorage<Productelement>
     */
    protected ObjectStorage $servicefeeElements;
    /**
     * @var ObjectStorage<FileReference>
     */
    protected $screenshots = null;

    /**
     * @var ObjectStorage<Package>
     */
    protected $packages;

    /**
     * @var ObjectStorage<Product>
     */
    protected $referenceProducts;

    /**
     * @var ObjectStorage<Product>
     */
    protected $linkedProducts;

    /**
     * @var FileReference
     */
    protected $factsheet = null;

    /**
     * @var ObjectStorage<Faq>
     */
    protected $faq;

    /**
     * @var User
     */
    protected $feuser = null;

    public function __construct()
    {
        $this->packages = new ObjectStorage();
        $this->screenshots = new ObjectStorage();
        $this->servicefeeElements = new ObjectStorage();
        $this->referenceProducts = new ObjectStorage();
        $this->linkedProducts = new ObjectStorage();
        $this->categories = new ObjectStorage();
        $this->leftContent = new ObjectStorage();
        $this->aiContent = new ObjectStorage();
        $this->faq = new ObjectStorage();
    }

    // Getter / Setter
    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }
    public function getSubname(): string
    {
        return $this->subname;
    }

    public function setSubname($subname): void
    {
        $this->subname = $subname;
    }
    public function getRendertype(): string
    {
        return $this->rendertype;
    }
    public function setRendertype($rendertype): void
    {
        $this->rendertype = $rendertype;
    }

    public function getShortdescription(): string
    {
        return $this->shortdescription;
    }

    public function setShortdescription(string $shortdescription): void
    {
        $this->shortdescription = $shortdescription;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    public function getImage(): ?FileReference
    {
        return $this->image;
    }

    public function setImage(FileReference $image): void
    {
        $this->image = $image;
    }

    public function getLeftContent(): ?ObjectStorage
    {
        return $this->leftContent;
    }

    public function setLeftContent(ObjectStorage $leftContent): void
    {
        $this->leftContent = $leftContent;
    }

    public function addLeftContent(Content $leftContent): void
    {
        $this->leftContent->attach($leftContent);
    }

    public function removeLeftContent(Content $leftContent): void
    {
        $this->leftContent->detach($leftContent);
    }

    public function getAiContent(): ?ObjectStorage
    {
        return $this->aiContent;
    }

    public function setAiContent(ObjectStorage $aiContent): void
    {
        $this->aiContent = $aiContent;
    }

    public function addAiContent(Content $aiContent): void
    {
        $this->aiContent->attach($aiContent);
    }

    public function removeAiContent(Content $aiContent): void
    {
        $this->aiContent->detach($aiContent);
    }
    

    public function getScreenshots(): ?ObjectStorage
    {
        return $this->screenshots;
    }

    public function setScreenshots(ObjectStorage $screenshots): void
    {
        $this->screenshots = $screenshots;
    }

    public function getPackages():  ?ObjectStorage
    {
            return $this->packages;
        }

    public function setPackages($packages):  void
    {
        $this->packages = $packages;
    }
    public function getCategories(): ObjectStorage
    {
            return $this->categories;
        }

    public function setCategories($categories):  void
    {
        $this->categories = $cateories;
    }

    /**
     * @return ObjectStorage<Productelement>
     */
    public function getServicefeeElements(): ObjectStorage
    {
        return $this->servicefeeElements;
    }

    /**
     * @param ObjectStorage<Productelement> $servicefeeElements
     */
    public function setServicefeeElements(ObjectStorage $servicefeeElements): void
    {
        $this->servicefeeElements = $servicefeeElements;
    }

    /**
     * Add a single service fee element
     */
    public function addServicefeeElement(Productelement $element): void
    {
        $this->servicefeeElements->attach($element);
    }

    /**
     * Remove a single service fee element
     */
    public function removeServicefeeElement(Productelement $element): void
    {
        $this->servicefeeElements->detach($element);
    }

    public function getServiceFee(): float
    {
        $serviceFee = 0.0;
        foreach ($this->getServicefeeElements() as $servicefee_element) {
           $serviceFee += $servicefee_element->getPrice();
        
        }
        return $serviceFee;
    }

    public function getReferenceProducts(): ?ObjectStorage
    {
        return $this->referenceProducts;
    }

    public function setReferenceProducts(ObjectStorage $referenceProducts): void
    {
        $this->referenceProducts = $referenceProducts;
    }   

    public function getLinkedProducts(): ?ObjectStorage
    {
        return $this->linkedProducts;
    }   

    public function setLinkedProducts(ObjectStorage $linkedProducts): void
    {
        $this->linkedProducts = $linkedProducts;
    }  

    public function getFaq(): ?ObjectStorage
    {
        return $this->faq;
    }   

    public function setFaq(ObjectStorage $faq): void
    {
        $this->faq = $faq;
    }

    public function getFactsheet(): ?FileReference
    {
        return $this->factsheet;
    }

    public function setFactsheet(?FileReference $factsheet): void
    {
        $this->factsheet = $factsheet;
    }

    public function getFeuser(): ?User
    {
        return $this->feuser;
    }

    public function setFeuser(?User $feuser): void
    {
        $this->feuser = $feuser;
    }
}
