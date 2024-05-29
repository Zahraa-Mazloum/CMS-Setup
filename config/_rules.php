<?php

return function () {
    $friendlyRules = [];
    if(tier()->id == env('DEFAULT_TIER') && is_file(dirname(__DIR__) . "/runtime/friendly-url.php")){
        $friendlyRules = require_once dirname(__DIR__) . "/runtime/friendly-url.php";
    }

    $name = tier()->id;
    $file = __DIR__."/rules/_$name.php";

    $rules = [];
    if(file_exists($file)){
        $rules = require_once $file;
    }

    return [
        ...$friendlyRules,
        ...$rules,
        // default
        '' => '/default/index',
        '<controller>/<action>' => '<controller>/<action>',
        '<controller>' => '/<controller>/index',
        [
            'pattern' => '',
            'route' => '/default/index',
            'suffix' => '/',
        ],
    ];
};
