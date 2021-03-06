<?php

return [
    'code' => '237',
    'patterns' => [
        'national' => [
            'general' => '/^[237-9]\\d{7}$/',
            'fixed' => '/^(?:22|33)\\d{6}$/',
            'mobile' => '/^[79]\\d{7}$/',
            'tollfree' => '/^800\\d{5}$/',
            'premium' => '/^88\\d{6}$/',
            'emergency' => '/^1?1[37]$/',
        ],
        'possible' => [
            'general' => '/^\\d{8}$/',
            'emergency' => '/^\\d{2,3}$/',
        ],
    ],
];
