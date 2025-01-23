<?php

use Orlyapps\Printable\Support\StationeryResolver\DefaultStationeryResolver;

return [
    'middleware' => ['web', 'nova'],
    'stationery_resolver' => DefaultStationeryResolver::class,
    'tailwindConfig' => __DIR__ . "/../resources/tailwind.config.js",

    'gotenberg' => [
        'url' => env('GOTENBERG_URL'),
        'username' => env('GOTENBERG_USERNAME'),
        'password' => env('GOTENBERG_PASSWORD'),
    ],
];
