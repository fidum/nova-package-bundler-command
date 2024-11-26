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
    | Download url assets
    |--------------------------------------------------------------------------
    |
    | Set this value to `true` if you want the bundler command to download
    | assets where the path is already a url. When `false`, url assets are
    | ignored when bundling and nova will load them as normal.
    |
    */
    'download_url_assets' => false,

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

    /*
    |--------------------------------------------------------------------------
    | Versioning
    |--------------------------------------------------------------------------
    |
    | Enable versioning of generated assets. This will append a query string
    | to the asset path with a unique value based on the file content. This
    | is done by generating a manifest file that maps the original path to
    | the versioned path.
    |
    | Note: This feature requires the `manifest.json` file to be readable in
    | the public directory.
    |
    */
    'version' => [
        'enabled' => false,

        'manifest' => '/vendor/nova-tools/manifest.json',
    ],
];
