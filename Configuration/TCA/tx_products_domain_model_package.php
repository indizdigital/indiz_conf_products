<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_package',
        'label' => 'name',
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
        '1' => ['showitem' => '--palette--;;titleline,packageelements'],
    ],
    'palettes' => [
        'language' => ['showitem' => 'sys_language_uid, l10n_parent'],
        'titleline' => ['showitem' => 'name,desc,configurable'],
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
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_package.name',
            'config' => [
                'type' => 'input',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'desc' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_package.desc',
            'config' => [
                'type' => 'text',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'configurable' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_package.configurable',
            'config' => [
                'type' => 'check',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'packageelements' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_package.packageelements',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_products_domain_model_packageelement',
                'appearance' => [
                    'useSortable' => true,
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
    ],
];
