<?php
return [
    'ctrl' => [
        'title' => 'Package Element',
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
        'security' => [
            'ignorePageTypeRestriction' => true
        ],
    ],
    'types' => [
        '1' => ['showitem' => 'name,amount,amount,isbase,package,productelement,  --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
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
            'config' => ['type' => 'input']
        ],
        'amount' => [
            'config' => ['type' => 'input', 'eval' => 'int']
        ],
        'is_base' => [
            'config' => ['type' => 'check']
        ],
        'package' => [
            'config' => [
                'type' => 'select',
                'foreign_table' => 'tx_ext_domain_model_package',
                'maxitems' => 1,
            ]
        ],
        'productelement' => [
            'config' => [
                'type' => 'select',
                'foreign_table' => 'tx_ext_domain_model_productelement',
                'maxitems' => 1,
            ]
        ],
    ],
];
