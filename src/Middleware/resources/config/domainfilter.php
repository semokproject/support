<?php return [
    'redirect_domain' => [
        'semok.test' => 'www',
        'semok2.test' => 'non_www',
        'semok3.test' => [
            'type' => 'page',
            'page' => 'http://semokproject.com/page-target-url',
        ],
        'semok4.test' => [
            'type' => 'domain',
            'domain' => 'semokproject.com',
        ],
    ],
    'redirect_url' => [
        [
            'from' => 'http://domain.com/old-url',
            'to' => 'http://domain.com/new-url',
            'type' => 302
        ]
    ]
];
