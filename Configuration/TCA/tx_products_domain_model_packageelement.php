<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_packageelement',
        'label' => 'name',
        'sortby' => 'sorting',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'description,image,images',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'security' => [
            'ignorePageTypeRestriction' => true
        ],
    ],
    'types' => [
        '1' => ['showitem' => '--palette--;;line1,--palette--;;line2'],
    ],
    'palettes' => [
        'line1' => [
            'label' => '',
            'showitem' => 'productelement,name,subname,desc'
        ],
        'line2' => [
            'label' => '',
            'showitem' => 'formula,amount,min,max'
        ],
        'language' => ['showitem' => 'sys_language_uid, l10n_parent']
    ],
    'columns' => [
        
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'name' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_packageelement.name',
            'config' => [
                'type' => 'input',
                'behaviour' => ['allowLanguageSynchronization' => true],
            ]
        ],
        'subname' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_packageelement.subname',
            'config' => [
                'type' => 'input',
                'behaviour' => ['allowLanguageSynchronization' => true],
            ]
        ],
        'amount' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_packageelement.amount',
            'config' => [
                'type' => 'input',
                'eval' => 'int',
                'size' => 5,
                'behaviour' => ['allowLanguageSynchronization' => true],
            ]
        ],
        'min' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_packageelement.min',
            'config' => [
                'type' => 'input',
                'eval' => 'int',
                'size' => 5,
                'behaviour' => ['allowLanguageSynchronization' => true],
            ]
        ],
        'max' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_packageelement.max',
            'config' => [
                'type' => 'input',
                'eval' => 'int',
                'size' => 5,
                'behaviour' => ['allowLanguageSynchronization' => true],
            ]
        ],
        'desc' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_packageelement.desc',
            'config' => [
                'type' => 'text',
                'behaviour' => ['allowLanguageSynchronization' => true],
            ]
        ],
        'productelement' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_packageelement.productelement',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_products_domain_model_productelement',
                'behaviour' => ['allowLanguageSynchronization' => true],
            ]
        ],
        'formula' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_packageelement.formula',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'behaviour' => ['allowLanguageSynchronization' => true],
            ]
        ],
    ],
];
