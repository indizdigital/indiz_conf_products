<?php
defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::configurePlugin(
  'Products',
  'Index',
  [\Indiz\Products\Controller\ProductController::class => 'index,show,order'],
  [\Indiz\Products\Controller\ProductController::class => 'index,show,order'],
  'CType'
);

ExtensionUtility::configurePlugin(
  'Products',
  'Product',
  [\Indiz\Products\Controller\ProductController::class => 'show,order'],
  [\Indiz\Products\Controller\ProductController::class => 'show,order'],
  'CType'
);

ExtensionUtility::configurePlugin(
  'Products',
  'Import',
  [\Indiz\Products\Controller\ProductController::class => 'import'],
  [\Indiz\Products\Controller\ProductController::class => 'import'],
  'CType'
);

ExtensionUtility::configurePlugin(
  'Products',
  'Categories',
  [\Indiz\Products\Controller\CategoryController::class => 'shortlist'],
  [\Indiz\Products\Controller\CategoryController::class => 'shortlist'],
  'CType'
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\Indiz\Products\FormDataProvider\PackageElementInitializer::class] = [
    'depends' => [
        \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRecordDefaultValues::class,
    ],
    'before' => [
        \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInlineConfiguration::class,
    ],
];