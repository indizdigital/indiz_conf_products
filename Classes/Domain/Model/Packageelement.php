<?php

namespace Indiz\Products\Domain\Model;
use Indiz\Products\Services\Formula;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Packageelement extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    protected string $name = '';
    protected string $subname = '';
    protected string $formula = '';
    protected int $amount = 0;
    protected int $min = 0;
    protected int $max = 0;
    protected string $desc = '';
    /**
     * @var Productelement
     */
    protected $productelement = null;

    // Getter / Setter
	public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
	public function getSubname(): string
    {
        return $this->subname;
    }

    public function setSubname(string $subname): void
    {
        $this->subname = $subname;
    }
    
    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }
    public function getFormula(): string
    {
        return $this->formula;
    }

    public function setFormula($formula): void
    {
        $this->formula = $formula;
    }
    
    public function getMin(): int
    {
        return $this->min;
    }

    public function setMin($min): void
    {
        $this->min = $min;
    }
    
    public function getMax(): int
    {
        return $this->max;
    }

    public function setMax($max): void
    {
        $this->max = $max;
    }
    
    public function getProductelement(): Productelement
    {
        return $this->productelement;
    }

    public function setProductelement(Productelement $productelement): void
    {
        $this->productelement = $productelement;
    }

    public function getPrice():float{
        $price = 0.0;
        
        if($this->getProductelement()){
            $price = $this->getProductelement()->getPrice();
        }
        return $price;
    }

    public function getTotalPrice(){
        $price = 0.0;
        if($this->getFormula()){
            $formulaservice = GeneralUtility::makeInstance(Formula::class);
            $price = $formulaservice->calc($this->getFormula(),$this->getAmount());
        }else{
            $price = $this->getProductelement()->getPrice() * $this->getAmount();
        }
        
        return $price;
    }

    public function getFixFormula(){
        $formula = $this->getFormula();
        if(strlen($formula) == 0){
            $formula = "{" . $this->getProductelement()->getUniqid() . "} * {amount}";
        }elseif(strpos($formula,"{amount}") === false){
            $formula = sprintf("(%s)*{amount}",$formula);
        }
        $formulaservice = GeneralUtility::makeInstance(Formula::class);
        $formula = $formulaservice->calc($formula,$this->getAmount());
        return $formula;
    }

    public function getDesc(): string
    {
        return $this->desc;
    }

    public function setDesc($desc): void
    {
        $this->desc = $desc;
    }

}
