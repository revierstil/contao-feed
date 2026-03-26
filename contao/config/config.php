<?php

declare(strict_types=1);

use Contao\ArrayUtil;
use Revierstil\ContaoFeed\Model\FeedModel;

// Backend modules
if(array_key_exists('revierstil', $GLOBALS['BE_MOD']) === false) {
    ArrayUtil::arrayInsert(
        $GLOBALS['BE_MOD'],
        1,
        [
            'revierstil' => [],
        ]
    );
}

$GLOBALS['BE_MOD']['revierstil']['rs_feed']['tables'][] = FeedModel::getTable();