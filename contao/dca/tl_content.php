<?php

declare(strict_types=1);

// Palettes
$GLOBALS['TL_DCA']['tl_content']['metapalettes']['rs_feed_list'] = [
    'type'                  => ['type'],
    'expert'                => [':hide', 'guests', 'cssID'],
    'invisible'             => [':hide', 'invisible', 'start', 'stop'],
];
