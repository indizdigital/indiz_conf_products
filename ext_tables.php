<?php
defined('TYPO3') or die();




\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--div--;Configuration,pi_flexform,',
    'insights_tease',
    'after:subheader'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:products/Configuration/FlexForms/flexform_insight.xml',
    'insights_tease'
);