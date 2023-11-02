<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Output paths
    |--------------------------------------------------------------------------
    |
    | Define the output paths where the command will save the contents of the
    | bundled packages. These paths will be wrapped with `public_path` as
    | the output needs to always end up in the public directory so that
    | we can tell Nova to load it via the ASSET_URL.
    |
    */
    'paths' => [
        'script' => '/vendor/nova-tools/app.js',
        'style' => '/vendor/nova-tools/app.css',
    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded assets
    |--------------------------------------------------------------------------
    |
    | Define the assets to skip when bundling packages. The name of the script
    | or style with which it is added is expected here.
    |
    */
    'excluded' => [
        'scripts' => [],
        'styles' => [],
    ],
];
