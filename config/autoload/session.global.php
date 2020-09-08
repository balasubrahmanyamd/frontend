<?php

/**
 * @key remember_me_inactive sets the duration of the session (ms)
 * @key cookie_name sets the cookie name
 * @key name sets the session name
 * @key use_cookies, if this key is set to true, it will use cookies.
 * @key cookie_lifetime sets the duration of the cookie (cookie lifetime) in ms
 */

return [
    'dot_flashmessenger' => [
        'options' => [
            'namespace' => 'frontend_messenger'
        ]
    ],

    'dot_session' => [
        'remember_me_inactive' => 60 * 60 * 24, // 1 day session lifetime
        'cookie_name' => 'remember_me_token',
    ],

    'session_config' => [
        'name' => 'dot_session',
        'use_cookies' => true,
        'cookie_secure' => false,
        'cookie_httponly' => true,
        'remember_me_seconds' => 10,
        'cookie_lifetime' => 60 * 60 * 24 * 30, // 1 month lifetime
        'cookie_path' => '/',
        'gc_maxlifetime' => 1000,
    ],

    'session_containers' => [
        'user'
    ]
];
