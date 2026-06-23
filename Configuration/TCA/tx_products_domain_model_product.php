<?php
return [
    'ctrl' => [
        'title' => 'Product',
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
        '1' => ['showitem' => '--palette--;;titleline,--palette--;;subtit,--palette--;;render,shortdescription,description,--div--;Filter,categories,tags,--div--;Akkordeon/Berater,accordeon,feuser,--div--;Karuselle,altcontent,reference_products,screenshots,--div--;Pakete,packagetitle,packages,--div--;AI Inhalt,ai_content,--div--;FAQ,faq,  --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
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
            'label' => 'Slug',
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
            'label' => 'Titel',
            'config' => ['type' => 'input']
        ],
        'subname' => [
            'label' => 'Untertitel',
            'config' => ['type' => 'input']
        ],
        'rendertype' => [
            'label' => 'Rendertype',
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
            'label' => 'Kurzbeschreibung',
            'config' => ['type' => 'text']
        ],
        'description' => [
            'label' => 'Beschreibung',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ]
        ],
        'accordeon' => [
            'label' => 'Akkordeon',
                'config' => [
                    'type' => 'inline',
                    'foreign_table' => 'tt_content',
                    'maxitems' => 1
                ]
        ],
        'altcontent' => [
            'label' => 'Leistungsumfang',
                'config' => [
                    'type' => 'inline',
                    'foreign_table' => 'tt_content',
                    'maxitems' => 1
                ]
        ],
        'ai_content' => [
            'label' => 'AI Inhalt',
            'displayCond' => 'FIELD:rendertype:=:ai',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tt_content',
            ]
        ],
        'categories' => [
            'label' => 'Kategorie',
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
            'label' => 'Tags',
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
            'label' => 'Referenzprodukte',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_products_domain_model_product',
            ]
        ],
        
        'image' => [
            'label' => 'Bild',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'file',
                'maxitems' => 1
            ]
        ],
        'subimage' => [
            'label' => 'Icon',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'file',
                'maxitems' => 1
            ]
        ],
        'screenshots' => [
            'label' => 'Screenshots',
            'l10n_mode' => 'exclude',
            'config' => [
                    'type' => 'inline',
                    'foreign_table' => 'tt_content',
                    'maxitems' => 1
            ]
        ],
        'packagetitle' => [
            'label' => 'Package title',
            'config' => [
                'type' => 'input',
            ]
        ],
        'packages' => [
            'label' => 'Pakete',
            'exclude' => true,
            'displayCond' => 'FIELD:rendertype:!=:ai',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_products_domain_model_package',
            ]
        ],
        #reload element on change
        'faq' => [
            'label' => 'Pakete',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_products_domain_model_faq',
                'foreign_table_where' => ' AND tx_products_domain_model_faq.sys_language_uid IN (0,-1) ORDER BY name',
            ]
        ],
        'feuser' => [
            'label' => 'Beratung',
            'l10n_mode' => 'exclude',
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
