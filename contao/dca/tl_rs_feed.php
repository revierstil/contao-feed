<?php

declare(strict_types=1);

use Contao\DataContainer;

$GLOBALS['TL_DCA']['tl_rs_feed'] = [

    // Config
    'config' => [
        'dataContainer'    => Contao\DC_Table::class,
        'switchToEdit'     => true,
        'enableVersioning' => true,
        'sql'              => [
            'keys' => [
                'id'       => 'primary',
                'author'   => 'index',
                'location' => 'index',
                'likes'    => 'index',
            ],
        ],
    ],

    // List
    'list'   => [
        'sorting' => [
            'mode'            => DataContainer::MODE_SORTABLE,
            'fields'          => ['dateCreated'],
            'panelLayout'     => 'limit;sort',
            'disableGrouping' => true,
        ],
        'label'   => [
            'fields' => ['message'],
            'label'  => '%s',
        ],
    ],

    'metapalettes' => [
        'default' => [
            'meta'    => ['author', 'location', 'likes', 'dateCreated'],
            'message' => ['message', 'image'],
            'publish' => ['published'],
        ],
    ],

    // Fields
    'fields'       => [
        'id'          => [
            'sql' => ['type' => 'integer', 'autoincrement' => true],
        ],
        'tstamp'      => [
            'sql' => ['type' => 'integer', 'length' => 10, 'default' => 0],
        ],
        'likes'       => [
            'inputType' => 'text',
            'sorting'   => true,
            'eval'      => ['tl_class' => 'w50', 'readonly' => true, 'rgxp' => 'natural'],
            'sql'       => ['type' => 'integer', 'length' => 10, 'default' => 0],
        ],
        'author'      => [
            'exclude'    => true,
            'search'     => true,
            'inputType'  => 'select',
            'foreignKey' => 'tl_member.username',
            'eval'       => ['tl_class' => 'w50', 'chosen' => true, 'mandatory' => true],
            'sql'        => ['type' => 'integer', 'length' => 10, 'default' => 0],
            'relation'   => ['type' => 'hasOne', 'load' => 'lazy'],
        ],
        'location'    => [
            'exclude'    => true,
            'search'     => true,
            'inputType'  => 'select',
            'foreignKey' => 'tl_bio_option_group_option.title',
            'eval'       => ['tl_class' => 'w50', 'chosen' => true, 'mandatory' => true, 'option_group' => 'location'],
            'sql'        => ['type' => 'integer', 'length' => 10, 'default' => 0],
            'relation'   => ['type' => 'hasOne', 'load' => 'lazy'],
        ],
        'message'     => [
            'inputType' => 'textarea',
            'eval'      => ['tl_class' => 'clr', 'mandatory' => true, 'allowHtml' => false, 'maxlength' => 300],
            'sql'       => ['type' => 'text', 'notnull' => false, 'default' => ''],
        ],
        'image'       => [
            'inputType' => 'fileTree',
            'eval'      => ['fieldType' => 'radio', 'filesOnly' => true, 'tl_class' => 'clr'],
            'sql'       => ['type' => 'binary_string', 'notnull' => false, 'default' => null],
        ],
        'published'   => [
            'inputType' => 'checkbox',
            'filter'    => true,
            'toggle'    => true,
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => ['type' => 'boolean', 'default' => 0],
        ],
        'dateCreated' => [
            'inputType' => 'text',
            'sorting'   => true,
            'eval'      => ['rgxp' => 'datim', 'tl_class' => 'w50', 'readonly' => true],
            'sql'       => ['type' => 'integer', 'length' => 10, 'default' => 0],
        ],
    ],
];
