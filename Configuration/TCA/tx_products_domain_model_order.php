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
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'description,image,images',
        'security' => [
            'ignorePageTypeRestriction' => true
        ],
    ],
    'types' => [
        '1' => ['showitem' => 'ordername,name,email,street,postalcode,city,country,package_uid,product_uid,  --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
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
        'ordername' => [
            'label' => 'Bestellname',
            'config' => ['type' => 'input']
        ],
        'name' => [
            'label' => 'Name',
            'config' => ['type' => 'input']
        ],
        'email' => [
            'label' => 'Email',
            'config' => ['type' => 'input']
        ],
        'street' => [
            'label' => 'Straße',
            'config' => ['type' => 'input']
        ],
        'postalcode' => [
            'label' => 'PLZ',
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
        'package_uid' => [
            'label' => 'Paket UID',
            'config' => ['type' => 'input', 'eval' => 'int']
        ],
        'product_uid' => [
            'label' => 'Produkt UID',
            'config' => ['type' => 'input', 'eval' => 'int']   
        ],
       
    ],
];
