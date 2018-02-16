<?php return [
    'enable' => true,
    'pagespeed' => [
        /*
        |--------------------------------------------------------------------------
        | Enable Laravel Page Speed
        |--------------------------------------------------------------------------
        |
        | Set this field to false to disable the laravel page speed service.
        | You would probably replace that in your local configuration to get a readable output.
        |
        */
        'enabled' => env('LARAVEL_PAGE_SPEED_ENABLE', true),
        'groups' => [
            'inline-css',
            'elide-attributes',
            'insert-dns-prefetch',
            'remove-comments',
            'trim-urls',
            'remove-quotes',
            'collapse-whitespace',
        ],
        /*
        |--------------------------------------------------------------------------
        | Skip Routes
        |--------------------------------------------------------------------------
        |
        | Skip Routes paths to exclude.
        | You can use * as wildcard.
        |
        */

        'skip' => [
            '*.xml',
            '*.less',
            '*.pdf',
            '*.doc',
            '*.txt',
            '*.ico',
            '*.rss',
            '*.zip',
            '*.mp3',
            '*.rar',
            '*.exe',
            '*.wmv',
            '*.doc',
            '*.avi',
            '*.ppt',
            '*.mpg',
            '*.mpeg',
            '*.tif',
            '*.wav',
            '*.mov',
            '*.psd',
            '*.ai',
            '*.xls',
            '*.mp4',
            '*.m4a',
            '*.swf',
            '*.dat',
            '*.dmg',
            '*.iso',
            '*.flv',
            '*.m4v',
            '*.torrent'
        ],
    ],

    'responsecache' => [
        /*
         * Determine if the response cache middleware should be enabled.
         */
        'enabled' => env('RESPONSE_CACHE_ENABLED', true),

        /*
         *  The given class will determinate if a request should be cached. The
         *  default class will cache all successful GET-requests.
         *
         *  You can provide your own class given that it implements the
         *  CacheProfile interface.
         */
        'cache_profile' => Spatie\ResponseCache\CacheProfiles\CacheAllSuccessfulGetRequests::class,

        /*
         * When using the default CacheRequestFilter this setting controls the
         * default number of minutes responses must be cached.
         */
        'cache_lifetime_in_minutes' => env('RESPONSE_CACHE_LIFETIME', 60 * 24 * 7),

        /*
         * This setting determines if a http header named "Laravel-responsecache"
         * with the cache time should be added to a cached response. This
         * can be handy when debugging.
         */
        'add_cache_time_header' => false,

        /*
         * Here you may define the cache store that should be used to store
         * requests. This can be the name of any store that is
         * configured in app/config/cache.php
         */
        'cache_store' => env('RESPONSE_CACHE_DRIVER', 'file'),

        /*
         * If the cache driver you configured supports tags, you may specify a tag name
         * here. All responses will be tagged. When clearing the responsecache only
         * items with that tag will be flushed.
         *
         * You may use a string are an array here.
         */
        'cache_tag' => '',
    ]
];
