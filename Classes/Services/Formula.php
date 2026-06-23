<?php
namespace Indiz\Products\Services;

use Indiz\Products\Domain\Repository\ProductelementRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Formula
{
    private $productelementRepository;

    public function __construct(
    ) {

        $this->productelementRepository = GeneralUtility::makeInstance(ProductelementRepository::class);
    }

    public function calc(string $formula,int $amount)
    {
        //if amount == 0 keep it as string for further calculations
        if($amount){
            $formula = str_replace('{amount}',$amount, $formula);
        }
        preg_match_all('/\{([^}]+)\}/', $formula, $matches);

        $expression = $formula;
        foreach ($matches[1] as $slug) {
            if($slug != "amount"){
                $element = $this->productelementRepository->findByUniqid($slug);
                $price = $element !== null ? $element->getPrice() : 0.0;
                $expression = str_replace('{' . $slug . '}', (string)$price, $expression);
            }
        } 
        

        if (!$amount) {
            return $expression;
        }

        // Strip everything except digits, decimals, operators and parentheses
        if($amount == 0){
            $expression = preg_replace('/[^0-9+\-*\/().\s]/', '', $expression);
        }
        return (float)eval('return ' . $expression . ';');
    }
}
