<?php

return [

    'web' => [

        'provider' => env('SEARCH_PROVIDER', 'bing'),

        'api_key' => env('SEARCH_API_KEY'),

        'endpoint' => env(
            'SEARCH_ENDPOINT',
            'https://api.bing.microsoft.com/v7.0/search'
        ),

    ],

];