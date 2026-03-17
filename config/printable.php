<?php

use Orlyapps\Printable\Support\StationeryResolver\DefaultStationeryResolver;

return [
    'middleware' => ['web', 'nova'],
    'stationery_resolver' => DefaultStationeryResolver::class,
    'tailwindConfig' => __DIR__ . "/../resources/tailwind.config.js",

    /**
     * Position der Seitenzahl: 'header' oder 'footer'
     */
    'page_number_position' => 'header',

    'gotenberg' => [
        'url' => env('GOTENBERG_URL'),
    ],
];
