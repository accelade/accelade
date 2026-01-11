<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Frontend Framework
    |--------------------------------------------------------------------------
    |
    | Specify which frontend framework to use with Accelade. Choose between
    | 'vue' or 'react'. This determines which pre-compiled assets will be
    | served to the browser.
    |
    */
    'framework' => env('ACCELADE_FRAMEWORK', 'vanilla'),

    /*
    |--------------------------------------------------------------------------
    | Asset Serving Mode
    |--------------------------------------------------------------------------
    |
    | Determines how Accelade serves its JavaScript assets.
    | 'route' - Serve via Laravel route (default, no publishing needed)
    | 'published' - Serve from published assets in public/vendor/accelade
    |
    */
    'asset_mode' => env('ACCELADE_ASSET_MODE', 'route'),

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | The URL prefix for Accelade routes (asset serving and AJAX sync).
    |
    */
    'prefix' => 'accelade',

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware to apply to Accelade routes.
    |
    */
    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | State TTL
    |--------------------------------------------------------------------------
    |
    | Time-to-live in seconds for server-synced component state.
    | Default is 1 hour (3600 seconds).
    |
    */
    'state_ttl' => env('ACCELADE_STATE_TTL', 3600),

    /*
    |--------------------------------------------------------------------------
    | Sync Debounce
    |--------------------------------------------------------------------------
    |
    | Debounce time in milliseconds for server sync requests.
    | Prevents excessive server calls during rapid state changes.
    |
    */
    'sync_debounce' => env('ACCELADE_SYNC_DEBOUNCE', 300),

    /*
    |--------------------------------------------------------------------------
    | Progress Bar Configuration
    |--------------------------------------------------------------------------
    |
    | Customize the SPA navigation progress bar appearance.
    | All options are optional - defaults will be used if not specified.
    |
    | Available options:
    | - delay: Delay in ms before showing progress bar (default: 250)
    | - color: Progress bar primary color (default: '#6366f1')
    | - gradientColor: Secondary gradient color (default: '#8b5cf6')
    | - gradientColor2: Third gradient color (default: '#a855f7')
    | - useGradient: Use gradient colors (default: true)
    | - height: Progress bar height in pixels (default: 3)
    | - showBar: Show progress bar (default: true)
    | - includeSpinner: Include spinner indicator (default: true)
    | - spinnerSize: Spinner size in pixels (default: 18)
    | - spinnerPosition: 'top-left', 'top-right', 'bottom-left', 'bottom-right' (default: 'top-right')
    | - position: 'top' or 'bottom' (default: 'top')
    | - minimum: Minimum progress percentage on start (default: 8)
    | - easing: Animation easing function (default: 'ease-out')
    | - speed: Animation speed in ms (default: 200)
    | - trickleSpeed: How fast progress trickles (default: 200)
    | - zIndex: Z-index for the progress bar (default: 99999)
    |
    */
    'progress' => [
        'color' => '#6366f1',
        'gradientColor' => '#8b5cf6',
        'gradientColor2' => '#a855f7',
        'height' => 3,
        'showBar' => true,
        'includeSpinner' => true,
        'spinnerPosition' => 'top-right',
        'position' => 'top',
    ],

    /*
    |--------------------------------------------------------------------------
    | Demo Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the built-in demo pages for testing and development.
    | The demo showcases all Accelade features across all supported frameworks.
    |
    | Options:
    | - enabled: Enable/disable demo routes (default: false in production)
    | - prefix: URL prefix for demo routes (default: 'demo')
    | - middleware: Middleware to apply to demo routes (default: ['web'])
    |
    */
    'demo' => [
        'enabled' => env('ACCELADE_DEMO_ENABLED', env('APP_ENV') !== 'production'),
        'prefix' => env('ACCELADE_DEMO_PREFIX', 'demo'),
        'middleware' => ['web'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Testing Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for running Accelade's test suite.
    |
    | Options:
    | - base_url: Base URL for E2E tests (default: APP_URL)
    |
    */
    'testing' => [
        'base_url' => env('ACCELADE_TEST_URL', env('APP_URL', 'http://localhost')),
    ],
];
