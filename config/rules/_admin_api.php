<?php 

return [
    'POST login' => '/default/login',
    'POST uploadImage' => '/default/upload-image',

    // admin
    'GET admins' => '/admin/index',
    'POST admins' => '/admin/create',
    'PUT admins/<id:\d+>' => '/admin/update',
    'DELETE admins/<id:\d+>' => '/admin/delete',

    // category
    'GET categories' => '/category/index',
    'POST categories' => '/category/create',
    'PUT categories/<id:\d+>' => '/category/update',
    'DELETE categories/<id:\d+>' => '/category/delete',

    // content
    'GET contents' => '/content/index',
    'POST contents' => '/content/create',
    'PUT contents/<id:\d+>' => '/content/update',
    'DELETE contents/<id:\d+>' => '/content/soft-delete',

    // tag
    'GET tags' => '/tag/index',
    'POST tags' => '/tag/create',
    'PUT tags/<id:\d+>' => '/tag/update',
    'DELETE tags/<id:\d+>' => '/tag/delete',

    // seo
    'GET seo' => '/seo/index',
    'POST seo' => '/seo/create',
    'PUT seo/<id:\d+>' => '/seo/update',
    'DELETE seo/<id:\d+>' => '/seo/delete',

    // friendly url
    'GET friendlyUrl' => '/friendly-url/index',
    'POST friendlyUrl' => '/friendly-url/create',
    'PUT friendlyUrl/<id:\d+>' => '/friendly-url/update',
    'DELETE friendlyUrl/<id:\d+>' => '/friendly-url/delete',

    // media
    'POST media/image' => '/media/upload-image',
    'POST media/file' => '/media/upload-file',

    // Statistics
    'GET statistic' => '/statistic/numbers',

    // settings
    'GET settings/references' => '/setting/references',
    'POST settings/references' => '/setting/save-reference',
    'GET settings/system' => '/setting/system',
    'POST settings/system' => '/setting/save-system',
];
