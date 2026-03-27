<?php

namespace App\Config;

use CodeIgniter\Config\Filters as BaseFilters;

class Filters extends BaseFilters
{
    public $aliases = [
        'csrf'     => \CodeIgniter\Filters\CSRF::class,
        'toolbar'  => \CodeIgniter\Filters\DebugToolbar::class,
        'honeypot' => \CodeIgniter\Filters\Honeypot::class,
        'auth'     => \App\Filters\AuthFilter::class,
    ];

    public $required = [
        'before' => [
            'web' => ['csrf'],
            'api'  => ['csrf'],
        ],
        'after' => [
            'web' => ['toolbar'],
            'api'  => ['toolbar'],
        ],
    ];

    public $globals = [
        'before' => [
            // 'honeypot',
        ],
        'after' => [
            'toolbar',
            // 'honeypot',
        ],
    ];

    public $methods = [
        'post' => ['csrf'],
    ];
}
