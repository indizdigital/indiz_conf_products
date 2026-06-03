<?php
namespace Indiz\Products\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Indiz\Products\Domain\Repository\CategoryRepository;

class CategoryController extends ActionController
{
    public function __construct(
        protected readonly CategoryRepository $categoryRepository
    ) {}

  public function shortlistAction(): \Psr\Http\Message\ResponseInterface
  {
      $this->view->assign("categories",$this->categoryRepository->findAll());
    return $this->htmlResponse();
  }
}
