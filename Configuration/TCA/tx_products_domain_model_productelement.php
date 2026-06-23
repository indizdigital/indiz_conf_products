<?php
return [
    'ctrl' => [
        'title' => 'Price',
        'label' => 'name',
        'altLabel' => 'unit',
        'forceAltLabel' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'default_sortby' => 'name',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'name,price,min,max',
        'security' => [
            'ignorePageTypeRestriction' => true
        ],
    ],
    'types' => [
        '1' => ['showitem' => 'name,price,elementtype,unit,uniqid,min,max,  --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
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
            'label' => 'Name',
            'config' => ['type' => 'input']
        ],
        'price' => [
            'label' => 'Price',
            'config' => ['type' => 'input', 'eval' => 'double2']
        ],
        'uniqid' => [
            'label' => 'Human readable Id',
            'config' => [
                'type' => 'slug', 
                'eval' => 'uniqueInSite',
                'generatorOptions' => [
                    'fields' => ['name'],
                ]
            ]
        ],
        'elementtype' => [
            'label' => 'Typ',
            'config' => [
                'type' => 'select',
                'renderType'=>'selectSingle',
                'items'=>[
                    ['Range',0],
                    ['Input',1]
                ]
            ]
        ],
        'unit' => [
            'label' => 'Unit',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:products/Resources/Private/Language/locallang.xlf:tx_products_domain_model_product.service_fee','service_fee'],
                    ['LLL:EXT:products/Resources/Private/Language/locallang.xlf:tx_products_domain_model_product.unit_cpu','unit_cpu'],
                    ['LLL:EXT:products/Resources/Private/Language/locallang.xlf:tx_products_domain_model_product.unit_gpu','unit_gpu'],
                    ['LLL:EXT:products/Resources/Private/Language/locallang.xlf:tx_products_domain_model_product.unit_gb','unit_gb'],
                    ['LLL:EXT:products/Resources/Private/Language/locallang.xlf:tx_products_domain_model_product.unit_gib','unit_gib'],
                    ['LLL:EXT:products/Resources/Private/Language/locallang.xlf:tx_products_domain_model_product.unit_tb','unit_tb'],
                    ['LLL:EXT:products/Resources/Private/Language/locallang.xlf:tx_products_domain_model_product.unit_tib','unit_tib'],
                    ['LLL:EXT:products/Resources/Private/Language/locallang.xlf:tx_products_domain_model_product.unit_server','unit_server'],
                    ['LLL:EXT:products/Resources/Private/Language/locallang.xlf:tx_products_domain_model_product.unit_ipv4','unit_ipv4'],
                    ['LLL:EXT:products/Resources/Private/Language/locallang.xlf:tx_products_domain_model_product.unit_mtok','unit_mtok'],
                    ['LLL:EXT:products/Resources/Private/Language/locallang.xlf:tx_products_domain_model_product.unit_node','unit_node'],
                    ['LLL:EXT:products/Resources/Private/Language/locallang.xlf:tx_products_domain_model_product.unit_lbaas','unit_lbaas'],
                ],
            ],
        ],
        'min' => [
            'label' => 'Min',
            'config' => ['type' => 'input', 'eval' => 'int']
        ],
        'max' => [
            'label' => 'Max',
            'config' => ['type' => 'input', 'eval' => 'int']
        ],
    ],
];
