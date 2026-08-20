<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product',
        'label' => 'name',
        'label_alt' => 'subname',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'name,subname',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
        'security' => [
            'ignorePageTypeRestriction' => true
        ],
    ],
    'types' => [
        '1' => ['showitem' => '--palette--;;titleline,--palette--;;subtit,--palette--;;render,shortdescription,description,--div--;LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.filter,categories,tags,--div--;LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.consultant,accordeon,feuser,contactlabel,contactlink,--div--;LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.carousel,altcontent,reference_products,screenshots,--div--;LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.packages,packagetitle,packages,--div--;LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.aicontent,ai_content,--div--;LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.faq,faq,  --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
    ],
    'palettes' => [
        'render' => ['showitem' => 'rendertype,sys_language_uid,slug'],
        'titleline' => ['showitem' => 'name,image'],
        'subtit' => ['showitem' => 'subname, subimage'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => '', 'value' => 0],
                ],
                'foreign_table' => 'tx_products_domain_model_product',
                'foreign_table_where' => 'AND {#tx_products_domain_model_product}.{#pid}=###CURRENT_PID### AND {#tx_products_domain_model_product}.{#sys_language_uid} IN (-1,0)',
                'default' => 0,
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'l10n_source' => [
            'config' => [
                'type' => 'passthrough',
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
        'slug' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.slug',
            'config' => [
                'type' => 'slug', 
                'eval' => 'uniqueInSite',
                'generatorOptions' => [
                    'fields' => ['name', 'subname'],
                    'fieldSeparator' => '-',
                ]
            ]
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
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.name',
            'config' => ['type' => 'input']
        ],
        'subname' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.subname',
            'config' => ['type' => 'input']
        ],
        'rendertype' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.rendertype',
            'onChange' => 'reload',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['Pakete', 'packages'],
                    ['Personalisierung', 'personalised'],
                    ['AI','ai']
                ],
            ]
        ],
        'shortdescription' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.shortdescription',
            'config' => ['type' => 'text']
        ],
        'description' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.description',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ]
        ],
        'accordeon' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.accordeon',
                'config' => [
                    'type' => 'inline',
                    'foreign_table' => 'tt_content',
                    'maxitems' => 1
                ]
        ],
        'altcontent' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.altcontent',
                'config' => [
                    'type' => 'inline',
                    'foreign_table' => 'tt_content',
                    'maxitems' => 10
                ]
        ],
        'ai_content' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.aicontent',
            'displayCond' => 'FIELD:rendertype:=:ai',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tt_content',
            ]
        ],
        'categories' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.categories',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_products_domain_model_category',
                'foreign_table_where' => ' AND tx_products_domain_model_category.sys_language_uid IN (0,-1)',
                'MM' => 'tx_products_product_category',
                'minitems' => 1,
            ]
        ],
        'tags' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.tags',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_products_domain_model_tag',
                'foreign_table_where' => ' AND tx_products_domain_model_tag.sys_language_uid IN (0,-1) ORDER BY name',
                'MM' => 'tx_products_product_tag',
                'minitems' => 0,
            ]
        ],
        'reference_products' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.reference_products',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_products_domain_model_product',
            ]
        ],
        
        'image' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.image',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'file',
                'maxitems' => 1
            ]
        ],
        'subimage' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.subimage',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'file',
                'maxitems' => 1
            ]
        ],
        'screenshots' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.screenshots',
            'exclude' => true,
            'config' => [
                    'type' => 'inline',
                    'foreign_table' => 'tt_content',
                    'maxitems' => 1,
                    'behaviour' => [
                        'allowLanguageSynchronization' => true
                    ]
            ]
        ],
        'packagetitle' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.packagetitle',
            'config' => [
                'type' => 'input',
            ]
        ],
        'contactlabel' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.contactlabel',
            'config' => [
                'type' => 'input',
            ]
        ],
        'contactlink' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.contactlink',
            'config' => [
                'type' => 'link',
            ]
        ],
        'packages' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.packages',
            'exclude' => true,
            'displayCond' => 'FIELD:rendertype:!=:ai',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_products_domain_model_package',
            ]
        ],
        #reload element on change
        'faq' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.faq',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_products_domain_model_faq',
                'foreign_table_where' => ' AND tx_products_domain_model_faq.sys_language_uid IN (0,-1) ORDER BY name',
            ]
        ],
        'feuser' => [
            'label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_product.feuser',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tt_content',
                'maxitems' => 1,
                /*'overrideChildTca' => [
                    'columns' => [
                        'CType' => [
                            'config' => [
                                'items' => [],
                            ],
                        ],
                    ],
                ]*/
            ]
        ],
    ],
];
