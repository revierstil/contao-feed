<?php

declare(strict_types=1);

use Contao\DataContainer;
use Revierstil\ContaoFeed\Model\FeedModel;

$GLOBALS['TL_DCA']['tl_rs_feed_liked'] = [

    // Config
    'config' => [
        'dataContainer'    => Contao\DC_Table::class,
        'switchToEdit'     => true,
        'enableVersioning' => true,
        'ptable'           => FeedModel::getTable(),
        'sql'              => [
            'keys' => [
                'id'         => 'primary',
                'pid'        => 'index',
                'member'     => 'index',
                'member,pid' => 'index',
            ],
        ],
    ],

    // List
    'list'   => [
        'sorting' => [
            'mode' => DataContainer::MODE_PARENT,
        ],
    ],

    // Fields
    'fields' => [
        'id'     => [
            'sql' => ['type' => 'integer', 'autoincrement' => true],
        ],
        'pid'    => [
            'sql' => ['type' => 'integer', 'length' => 10, 'default' => 0],
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'length' => 10, 'default' => 0],
        ],
        'member' => [
            'sql' => ['type' => 'integer', 'length' => 10, 'default' => 0],
        ],
    ],
];
