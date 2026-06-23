<?php
defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::configurePlugin(
  'Products',
  'Index',
  [\Indiz\Products\Controller\ProductController::class => 'filter,index,show,order,finish'],
  [\Indiz\Products\Controller\ProductController::class => 'filter,index,show,order,finish'],
  'CType'
);

ExtensionUtility::configurePlugin(
  'Products',
  'Product',
  [\Indiz\Products\Controller\ProductController::class => 'show,order,finish'],
  [\Indiz\Products\Controller\ProductController::class => 'show,order,finish'],
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

ExtensionUtility::configurePlugin(
  'Insights',
  'Import',
  [\Indiz\Products\Controller\InsightController::class => 'import'],
  [\Indiz\Products\Controller\InsightController::class => 'import'],
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