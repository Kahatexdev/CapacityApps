<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array<string, class-string|list<class-string>> [filter_name => classname]
     *                                                     or [filter_name => [classname1, classname2, ...]]
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'capacity' => \App\Filters\CapacityAuth::class,
        'planning' => \App\Filters\PlanningAuth::class,
        'aps' => \App\Filters\ApsAuth::class,
        'user' => \App\Filters\UserFilter::class,
        'god' => \App\Filters\God::class,
        'sudo' => \App\Filters\Sudo::class,
        'ie' => \App\Filters\IEFilter::class,
        'cors' => \App\Filters\CorsFilter::class,
        'rosso' => \App\Filters\RossoFilter::class,
        'followup' => \App\Filters\FollowUp::class,

    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array<string, array<string, array<string, string>>>|array<string, list<string>>
     */
    public array $globals = [
        'before' => [
            'cors',
            // 'honeypot',
            // 'csrf',
            // 'invalidchars',
        ],
        'after' => [
            'toolbar',
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'post' => ['foo', 'bar']
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don't expect could bypass the filter.
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     */
    public array $filters = [
        'capacity' => [
            'before' => ['capacity/*'],
        ],
        'planning' => [
            'before' => ['planning/*'],
        ],
        'aps' => [
            'before' => ['aps/*'],
        ],
        'user' => [
            'before' => ['user/*'],
        ],
        'god' => [
            'before' => ['god/*'],
        ],
        'sudo' => [
            'before' => ['sudo/*'],
        ],
        'ie' => [
            'before' => ['ie/*'],
        ],
        'rosso' => [
            'before' => ['rosso/*'],
        ],
        'followup' => [
            'before' => ['followup/*'],
        ],
    ];
}
