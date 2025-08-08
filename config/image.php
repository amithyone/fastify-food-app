<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. You may choose one of them according to your PHP
    | configuration. By default PHP's "GD Library" implementation is used.
    |
    | Supported: "gd", "imagick"
    |
    */

    'driver' => env('IMAGE_DRIVER', 'gd'),

    /*
    |--------------------------------------------------------------------------
    | Image Cache
    |--------------------------------------------------------------------------
    |
    | Here you may specify if image caching should be used. By default
    | image caching is disabled. You can enable it by setting this
    | option to true.
    |
    */

    'cache' => [
        'enabled' => env('IMAGE_CACHE_ENABLED', false),
        'path' => env('IMAGE_CACHE_PATH', storage_path('framework/cache/image')),
    ],

];
