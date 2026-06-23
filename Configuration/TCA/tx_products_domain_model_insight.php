<?php

declare(strict_types=1);

defined('TYPO3') or die();

return [
    'ctrl' => [
        'title'                    => 'Insight',
        'label'                    => 'title',
        'sortby'                   => 'datetime',
        'default_sortby'           => 'datetime DESC',
        'crdate'                   => 'crdate',
        'tstamp'                   => 'tstamp',
        'languageField'            => 'sys_language_uid',
        'transOrigPointerField'    => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource'        => 'l10n_source',
        'delete'                   => 'deleted',
        'enablecolumns'            => [
            'disabled'  => 'hidden',
            'starttime' => 'starttime',
            'endtime'   => 'endtime',
            'fe_group'  => 'fe_group',
            'editlock'  => 'editlock',
        ],
        'editlock'      => 'editlock',
        'searchFields'  => 'title, alternative_title, teaser, bodytext, author, author_email, keywords, description',
        'security'      => [
            'ignorePageTypeRestriction' => true,
        ],
    ],

    'types' => [
        '0' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    --palette--;;titleandtype,
                    --palette--;;authors,
                    --palette--;;dates,
                    teaser,
                    bodytext,
                    path_segment,
                --div--;Media,
                    fal_media,
                    fal_related_files,
                --div--;Relations,
                    categories,
                    tags,
                    related_links,
                --div--;Meta,
                    keywords,
                    description,
                    --palette--;;urls,
                    istopnews,
                    notes,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    --palette--;;access
            ',
        ],
    ],

    'palettes' => [
        'titleandtype' => ['showitem' => 'title, alternative_title, type'],
        'authors'      => ['showitem' => 'author, author_email'],
        'dates'        => ['showitem' => 'datetime, archive'],
        'urls'         => ['showitem' => 'internalurl, externalurl'],
        'language'     => ['showitem' => 'sys_language_uid, l10n_parent'],
        'access'       => ['showitem' => 'hidden, --linebreak--, starttime, endtime, --linebreak--, fe_group, editlock'],
    ],

    'columns' => [

        'sys_language_uid' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config'  => ['type' => 'language'],
        ],

        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label'       => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config'      => [
                'type'                => 'select',
                'renderType'          => 'selectSingle',
                'items'               => [['label' => '', 'value' => 0]],
                'foreign_table'       => 'tx_products_domain_model_insight',
                'foreign_table_where' => 'AND {#tx_products_domain_model_insight}.{#pid}=###CURRENT_PID### AND {#tx_products_domain_model_insight}.{#sys_language_uid} IN (-1,0)',
                'default'             => 0,
            ],
        ],

        'l10n_diffsource' => ['config' => ['type' => 'passthrough']],
        'l10n_source'     => ['config' => ['type' => 'passthrough']],

        'hidden' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config'  => [
                'type'       => 'check',
                'renderType' => 'checkboxToggle',
                'items'      => [['label' => '', 'invertStateDisplay' => true]],
            ],
        ],

        'starttime' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config'  => [
                'type'      => 'datetime',
                'default'   => 0,
                'behaviour' => ['allowLanguageSynchronization' => true],
            ],
        ],

        'endtime' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config'  => [
                'type'      => 'datetime',
                'default'   => 0,
                'range'     => ['upper' => mktime(0, 0, 0, 1, 1, 2038)],
                'behaviour' => ['allowLanguageSynchronization' => true],
            ],
        ],

        'fe_group' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
            'config'  => [
                'type'          => 'select',
                'renderType'    => 'selectMultipleSideBySide',
                'size'          => 5,
                'maxitems'      => 20,
                'items'         => [
                    ['label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hide_at_login', 'value' => -1],
                    ['label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.any_login',     'value' => -2],
                    ['label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.usergroups',    'value' => '--div--'],
                ],
                'exclusiveKeys' => '-1,-2',
                'foreign_table' => 'fe_groups',
            ],
        ],

        'editlock' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:editlock',
            'config'  => [
                'type'       => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],

        'title' => [
            'label'  => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.title',
            'config' => [
                'type'     => 'input',
                'size'     => 50,
                'max'      => 255,
                'eval'     => 'trim',
                'required' => true,
            ],
        ],

        'alternative_title' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.alternative_title',
            'config'  => [
                'type' => 'input',
                'size' => 50,
                'max'  => 255,
                'eval' => 'trim',
            ],
        ],

        'teaser' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.teaser',
            'config'  => [
                'type' => 'text',
                'cols' => 60,
                'rows' => 5,
                'eval' => 'trim',
            ],
        ],

        'bodytext' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.bodytext',
            'config'  => [
                'type'             => 'text',
                'cols'             => 60,
                'rows'             => 20,
                'enableRichtext'   => true,
                'eval'             => 'trim',
            ],
        ],

        'datetime' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.datetime',
            'config'  => [
                'type'    => 'datetime',
                'format'  => 'datetime',
                'default' => 0,
            ],
        ],

        'archive' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.archive',
            'config'  => [
                'type'    => 'datetime',
                'format'  => 'datetime',
                'default' => 0,
            ],
        ],

        'author' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.author',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'max'  => 255,
                'eval' => 'trim',
            ],
        ],

        'author_email' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.author_email',
            'config'  => [
                'type' => 'email',
                'size' => 30,
            ],
        ],

        'type' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.type',
            'config'  => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => [
                    ['label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.type.0', 'value' => 0],
                    ['label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.type.1', 'value' => 1],
                    ['label' => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.type.2', 'value' => 2],
                ],
                'default' => 0,
            ],
        ],

        'keywords' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.keywords',
            'config'  => [
                'type' => 'input',
                'size' => 50,
                'max'  => 255,
                'eval' => 'trim',
            ],
        ],

        'description' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.description',
            'config'  => [
                'type' => 'text',
                'cols' => 60,
                'rows' => 5,
                'eval' => 'trim',
            ],
        ],

        'internalurl' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.internalurl',
            'config'  => [
                'type'         => 'link',
                'allowedTypes' => ['page'],
                'size'         => 50,
            ],
        ],

        'externalurl' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.externalurl',
            'config'  => [
                'type'         => 'link',
                'allowedTypes' => ['url'],
                'size'         => 50,
            ],
        ],

        'istopnews' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.istopnews',
            'config'  => [
                'type'       => 'check',
                'renderType' => 'checkboxToggle',
                'default'    => 0,
            ],
        ],

        'notes' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.notes',
            'config'  => [
                'type' => 'text',
                'cols' => 60,
                'rows' => 5,
                'eval' => 'trim',
            ],
        ],

        'path_segment' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.path_segment',
            'config'  => [
                'type'              => 'slug',
                'size'              => 50,
                'generatorOptions'  => [
                    'fields'       => ['title'],
                    'replacements' => ['/' => '-'],
                ],
                'fallbackCharacter' => '-',
                'eval'              => 'uniqueInSite',
                'default'           => '',
            ],
        ],

        'categories' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.categories',
            'config'  => [
                'type'                => 'select',
                'renderType'          => 'selectMultipleSideBySide',
                'foreign_table'       => 'tx_products_domain_model_insightcategory',
                'foreign_table_where' => 'ORDER BY tx_products_domain_model_insightcategory.name',
                'MM'                  => 'tx_products_domain_model_insight_insightcategory_mm',
                'size'                => 5,
                'maxitems'            => 100,
            ],
        ],

        'tags' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.tags',
            'config'  => [
                'type'                => 'select',
                'renderType'          => 'selectMultipleSideBySide',
                'foreign_table'       => 'tx_products_domain_model_insighttag',
                'foreign_table_where' => 'ORDER BY tx_products_domain_model_insighttag.name',
                'MM'                  => 'tx_products_domain_model_insight_insighttag_mm',
                'size'                => 5,
                'maxitems'            => 100,
            ],
        ],

        'related_links' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.related_links',
            'config'  => [
                'type'          => 'input',
            ],
        ],

        'fal_media' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.fal_media',
            'config'  => [
                'type'    => 'file',
                'allowed' => 'common-image-types',
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference',
                    'showPossibleLocalizationRecords' => true,
                    'showAllLocalizationLink'         => true,
                    'showSynchronizationLink'         => true,
                ],
                'overrideChildTca' => [
                    'types' => [
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '--palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,--palette--;;filePalette',
                        ],
                    ],
                ],
            ],
        ],

        'fal_related_files' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:products/Resources/Private/Language/locallang_db.xlf:tx_products_domain_model_insight.fal_related_files',
            'config'  => [
                'type'    => 'file',
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:media.addFileReference',
                    'showPossibleLocalizationRecords' => true,
                    'showAllLocalizationLink'         => true,
                    'showSynchronizationLink'         => true,
                ],
            ],
        ],

    ],
];
