<?php

namespace Indiz\Products\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use Indiz\Products\Domain\Model\Category;
use Indiz\Products\Domain\Model\Content;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
    protected $accordeon = null;
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
     * @var FileReference
     */
    protected $subimage = null;
    /**
     * @var ObjectStorage<Content>
     */
    protected $screenshots = null;
    protected string $packagetitle = "";

    /**
     * @var ObjectStorage<Package>
     */
    protected $packages;

    /**
     * @var ObjectStorage<Product>
     */
    protected $referenceProducts;

    /**
     * @var ObjectStorage<Content>
     */
    protected $altcontent;
    protected $altcontent_sorted = false;

    /**
     * @var ObjectStorage<Faq>
     */
    protected $faq;

    /**
     * @var ObjectStorage<Content>
     */
    protected $feuser = null;
    protected $contactlabel = "";
    protected $contactlink = "";

    public function __construct()
    {
        $this->packages = new ObjectStorage();
        $this->screenshots = new ObjectStorage();
        $this->referenceProducts = new ObjectStorage();
        $this->altcontent = new ObjectStorage();
        $this->feuser = new ObjectStorage();
        $this->categories = new ObjectStorage();
        $this->accordeon = new ObjectStorage();
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

    public function getSubimage(): ?FileReference
    {
        return $this->subimage;
    }

    public function setSubimage(FileReference $subimage): void
    {
        $this->subimage = $subimage;
    }

    public function getAccordeon(): ?ObjectStorage
    {
        return $this->accordeon;
    }

    public function setAccordeon(ObjectStorage $accordeon): void
    {
        $this->accordeon = $accordeon;
    }

    public function addAccordeon(Content $accordeon): void
    {
        $this->accordeon->attach($accordeon);
    }

    public function removeAccordeon(Content $accordeon): void
    {
        $this->accordeon->detach($accordeon);
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
    

    public function getAltcontent(): ?array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_products_domain_model_product');

        $rows = $queryBuilder
            ->select('altcontent')
            ->from('tx_products_domain_model_product')
            ->where(
                $queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter($this->uid))
            )
            ->executeQuery()
            ->fetchOne();

            
        $sorted = [];
        if($rows && strlen($rows)){
            $altcontent_uids = explode(",",$rows);
            foreach($this->altcontent as $altc){
                $pos = array_search($altc->getUid(),$altcontent_uids);
                $sorted[$pos] = $altc;
                /*if($this->uid == 28){
                    echo "pos".$pos.":".$altc->getUid()."<br>";
                }*/
            } 
        }else{
            return [];
        }
        ksort($sorted);
        $this->altcontent_sorted = true; 
        return ($sorted);


    }

    public function setAltcontent(ObjectStorage $altcontent): void
    {
        $this->altcontent = $altcontent;
    }
    public function getPackagetitle(): string
    {
        return $this->packagetitle;
    }

    public function setPackagetitle($packagetitle): void
    {
        $this->packagetitle = $packagetitle;
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

    public function setCategories($category):  void
    {
        $this->category = $cateory;
    } 

    public function getReferenceProducts(): ?ObjectStorage
    {
        return $this->referenceProducts;
    }

    public function setReferenceProducts(ObjectStorage $referenceProducts): void
    {
        $this->referenceProducts = $referenceProducts;
    } 

    public function getFaq(): ?ObjectStorage
    {
        return $this->faq;
    }   

    public function setFaq(ObjectStorage $faq): void
    {
        $this->faq = $faq;
    }

    public function getFeuser(): ?ObjectStorage
    {
        return $this->feuser;
    }

    public function setFeuser(?ObjectStorage $feuser): void
    {
        $this->feuser = $feuser;
    }

    public function getContactlabel(): string
    {
        return $this->contactlabel;
    }

    public function setContactlabel(string $contactlabel): void
    {
        $this->contactlabel = $contactlabel;
    }

    public function getContactlink(): string
    {
        return $this->contactlink;
    }

    public function setContactlink(string $contactlink): void
    {
        $this->contactlink = $contactlink;
    }
}
