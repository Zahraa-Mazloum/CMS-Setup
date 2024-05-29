<?php

return function () {
    return [
        '/cnd-admin/api' => [
            'id' => 'admin_api',
            'target' => 'api',
            'namespace' => 'app\\tiers\\admin\\api\\controllers',
        ],
        '/cnd-admin' => [
            'id' => 'admin',
            'target' => 'web',
            'namespace' => 'app\\tiers\\admin\\controllers',
            'api' => '/api',
        ],
        '/api' => [
            'id' => 'website_api',
            'target' => 'api',
            'namespace' => 'app\\tiers\\website\\api\\controllers',
        ],
        '/' => [
            'id' => 'website',
            'target' => 'web',
            'namespace' => 'app\\tiers\\website\\controllers',
            'api' => '/api',
        ],
    ];
};
