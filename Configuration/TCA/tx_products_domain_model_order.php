<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order',
        'label' => 'ordername',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'description,image,images',
        'security' => [
            'ignorePageTypeRestriction' => true
        ],
    ],
    'types' => [
        '1' => ['showitem' => 'ordername,ordertype,firstname,name,company,email,street,postalcode,city,country,package_uid,product_uid,data,  --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
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
        'ordername' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order.ordername',
            'config' => ['type' => 'input']
        ],
        'ordertype' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order.order_type',
            'config' => [
                'type' => 'check'
            ]
        ],
        'firstname' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order.firstname',
            'config' => ['type' => 'input']
        ],
        'name' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order.name',
            'config' => ['type' => 'input']
        ],
        'gender' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order.gender',
            'config' => ['type' => 'input']
        ],
        'email' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order.email',
            'config' => ['type' => 'input']
        ],
        'addressline' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order.addressline',
            'config' => ['type' => 'input']
        ],
        'addressline2' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order.addressline2',
            'config' => ['type' => 'input']
        ],
        'postalcode' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order.plz',
            'config' => ['type' => 'input']
        ],
        'phone' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order.phone',
            'config' => ['type' => 'input']
        ],
        'city' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order.city',
            'config' => ['type' => 'input']
        ],
        'country' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order.country',
            'config' => ['type' => 'input']
        ],
        'company' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order.company',
            'config' => ['type' => 'input']
        ],
        'package_uid' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order.package_uid',
            'config' => ['type' => 'input', 'eval' => 'int']
        ],
        'product_uid' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order.product_uid',
            'config' => ['type' => 'input', 'eval' => 'int']   
        ],
        'data' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order.data',
            'config' => ['type' => 'text']   
        ],
        'total' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order.total',
            'config' => ['type' => 'input']   
        ],
        'agb' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order.agb',
            'config' => ['type' => 'check']   
        ],
        'newsletter' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_order.newsletter',
            'config' => ['type' => 'check']   
        ],
       
    ],
];
