<?php
return [
    'ctrl' => [
        'title' => 'Product',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'description,images',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
        'security' => [
            'ignorePageTypeRestriction' => true
        ],
    ],
    'types' => [
        '1' => ['showitem' => '--palette--;;language,name,subname,rendertype,shortdescription,description,image,factsheet,categories,tags,screenshots,servicefee_elements,--div--;Akkordeon,left_content,feuser,--div--;Referenzenprodukte,linked_products,reference_products,--div--;Pakete,packages,--div--;AI Inhalt,ai_content,--div--;FAQ,faq,  --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
    ],
    'palettes' => [
        'language' => ['showitem' => 'sys_language_uid, l10n_parent'],
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
            'config' => ['type' => 'input']
        ],
        'description' => [
            'label' => 'Beschreibung',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ]
        ],
        'left_content' => [
            'label' => 'Inhalt',
                'config' => [
                    'type' => 'inline',
                    'foreign_table' => 'tt_content',
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
                'MM' => 'tx_products_product_tag',
                'minitems' => 1,
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
        'linked_products' => [
            'label' => 'Verknüpfte Produkte',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_products_domain_model_product',
            ]
        ],
        'image' => [
            'label' => 'Bild',
            'config' => [
                'type' => 'file',
                'maxitems' => 1
            ]
        ],
        'screenshots' => [
            'label' => 'Screenshots',
            'config' => [
                'type' => 'file',
                'maxitems' => 10
            ]
        ],
        'packages' => [
            'label' => 'Pakete',
            'displayCond' => 'FIELD:rendertype:!=:ai',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_products_domain_model_package',
            ]
        ],
        #reload element on change
        'servicefee_elements' => [
            'label' => 'Produktelement für Servicegebühr',
            'description' => 'Produktelement auswählen',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_products_domain_model_productelement',
                'foreign_table_where' => 'AND tx_products_domain_model_productelement.unit = "service_fee"',
                'minitems' => 0,
            ]
        ],
        'faq' => [
            'label' => 'Pakete',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_products_domain_model_faq',
            ]
        ],
        'factsheet' => [
            'label' => 'Factsheet',
            'config' => [
                'type' => 'file',
                'maxitems' => 1,
                'allowed' => 'pdf',
            ],
        ],
        'feuser' => [
            'label' => 'Feuser',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'items' => [
                    ["---",0]
                ]
            ]
        ],
    ],
];
