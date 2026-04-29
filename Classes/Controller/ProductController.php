<?php
namespace Indiz\Products\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class ProductController extends ActionController
{
  public function __construct(
  ) {}

  public function indexAction(): \Psr\Http\Message\ResponseInterface
  {
    
    return $this->htmlResponse();
  }
}