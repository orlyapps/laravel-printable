<?php

use Orlyapps\Printable\Support\StationeryResolver\DefaultStationeryResolver;

return [
    'middleware' => ['web', 'nova'],
    'stationery_resolver' => DefaultStationeryResolver::class,
    'tailwindConfig' => __DIR__ . "/../resources/tailwind.config.js",
    'timeout' => 5,
];
