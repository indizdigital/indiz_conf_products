<?php
defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::configurePlugin(
  'Products',
  'Index',
  [\Indiz\Products\Controller\ProductController::class => 'index'],
  [\Indiz\Products\Controller\ProductController::class => 'index'],
  'CType'
);
