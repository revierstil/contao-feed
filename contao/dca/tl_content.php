<?php

declare(strict_types=1);

// Palettes
$GLOBALS['TL_DCA']['tl_content']['metapalettes']['rs_feed_list'] = [
    'type'      => ['type'],
    'config'    => ['rs_feed_enableSlider'],
    'expert'    => [':hide', 'guests', 'cssID'],
    'invisible' => [':hide', 'invisible', 'start', 'stop'],
];

$GLOBALS['TL_DCA']['tl_content']['metasubpalettes']['rs_feed_slider'] = ['jumpTo'];

// Fields
$GLOBALS['TL_DCA']['tl_content']['fields']['rs_feed_enableSlider'] = [
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
    'sql'       => ['type' => 'boolean'],
];