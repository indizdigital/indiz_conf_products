<?php

namespace Indiz\Products\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;

class Category extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    protected string $name = '';
    protected string $description = '';
    protected string $shortdesc = '';
    protected string $shortbtn = '';
    protected $image = null;

    // Getter / Setter
	public function getName(): string
    {
	    return $this->name;
	}

    public function setName($name): void
    {
        $this->name = $name;
    }
 
    public function getDescription():  string
    {
            return $this->description;
        }

    public function setDescription($description):  void
    {
        $this->description = $description;
    }
 
    public function getShortbtn():  string
    {
            return $this->shortbtn;
        }

    public function setShortbtn($shortbtn):  void
    {
        $this->shortbtn = $shortbtn;
    }

 
    public function getShortdesc():  string
    {
            return $this->shortdesc;
        }

    public function setShortdesc($shortdesc):  void
    {
        $this->shortdesc = $shortdesc;
    }


    public function getImage():  FileReference
    {
            return $this->image;
        }

    public function setImage($image):  void
    {
        $this->image = $image;
    }
}
