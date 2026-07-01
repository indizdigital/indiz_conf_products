<?php

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::registerPlugin(
  'Products',
  'Index',
  'Index'
);
ExtensionUtility::registerPlugin(
  'Products',
  'Product',
  'Product'
);

ExtensionUtility::registerPlugin(
  'Products',
  'Import',
  'Import Prices'
);


ExtensionUtility::registerPlugin(
  'Products',
  'Categories',
  'Category Shortlist'
);


ExtensionUtility::registerPlugin(
  'Insights',
  'Import',
  'Import Insights'
);


ExtensionUtility::registerPlugin(
  'Insights',
  'View',
  'View Insights'
);


ExtensionUtility::registerPlugin(
  'Insights',
  'Tease',
  'Tease Insights'
);

$GLOBALS['TCA']['tt_content']['palettes']['frames']["showitem"] = $GLOBALS['TCA']['tt_content']['palettes']['frames']["showitem"] . ',--linebreak--,defaultopen';

$GLOBALS['TCA']['tt_content']['columns']['defaultopen'] = [
  "exclude" => 1,
  "label" => "Default open",
   "config" => [
    "type"=>"check",
    "renderType" => "checkboxToggle"
   ]
];
