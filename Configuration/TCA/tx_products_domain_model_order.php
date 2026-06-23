<?php
return [
    'ctrl' => [
        'title' => 'Order',
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
            'label' => 'Bestellname',
            'config' => ['type' => 'input']
        ],
        'ordertype' => [
            'label' => 'Order Type',
            'config' => [
                'type' => 'check'
            ]
        ],
        'firstname' => [
            'label' => 'Vorname',
            'config' => ['type' => 'input']
        ],
        'name' => [
            'label' => 'Name',
            'config' => ['type' => 'input']
        ],
        'gender' => [
            'label' => 'Gender',
            'config' => ['type' => 'input']
        ],
        'email' => [
            'label' => 'Email',
            'config' => ['type' => 'input']
        ],
        'addressline' => [
            'label' => 'Addressline',
            'config' => ['type' => 'input']
        ],
        'addressline2' => [
            'label' => 'Addressline2',
            'config' => ['type' => 'input']
        ],
        'postalcode' => [
            'label' => 'PLZ',
            'config' => ['type' => 'input']
        ],
        'phone' => [
            'label' => 'Phone',
            'config' => ['type' => 'input']
        ],
        'city' => [
            'label' => 'Stadt',
            'config' => ['type' => 'input']
        ],
        'country' => [
            'label' => 'Land',
            'config' => ['type' => 'input']
        ],
        'company' => [
            'label' => 'Firma',
            'config' => ['type' => 'input']
        ],
        'package_uid' => [
            'label' => 'Paket UID',
            'config' => ['type' => 'input', 'eval' => 'int']
        ],
        'product_uid' => [
            'label' => 'Produkt UID',
            'config' => ['type' => 'input', 'eval' => 'int']   
        ],
        'data' => [
            'label' => 'Data',
            'config' => ['type' => 'text']   
        ],
        'total' => [
            'label' => 'Total',
            'config' => ['type' => 'input']   
        ],
        'agb' => [
            'label' => 'AGB',
            'config' => ['type' => 'check']   
        ],
        'newsletter' => [
            'label' => 'Newsletter',
            'config' => ['type' => 'check']   
        ],
       
    ],
];
