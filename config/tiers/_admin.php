<?php

use yii\helpers\Url;

return [
    'language' => 'en',
    'params' => [
        'menu' => fn() => [
            ['mdiHome', 'Dashboard', Url::to(['/panel/index'], true), route() == "panel/index"],
            [
                'title' => ['mdiShapeOutline', 'CMS'],
                'items' => [
                    ...array_map(function($item){
                        return [$item['label'], Url::to(['/panel/contents', 'type' => $item['name']], true), route() == "panel/contents" && get("type") == $item['name']];
                    }, param('cms_type')),
                    ['SEO', Url::to(['/panel/seo'], true), route() == "panel/seo"]
                ],
            ],
            [
                'title' => ['mdiAccount', 'Users'],
                'items' => [
                    ['Admins', Url::to(['/panel/admins'], true), route() == "panel/admins"]
                ],
            ],
            ['mdiCog', 'Settings', Url::to(['/panel/settings'], true), route() == "panel/settings"],
        ],
    ],
];
