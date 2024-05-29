<?php

return function () {
    return [
        'basePath' => dirname(__DIR__),
        'id' => env('APP_ID', tier()->id) . '_' . tier()->id,
        'name' => env('APP_NAME', tier()->id),
        'version' => env('APP_VERSION'),
        'timeZone' => env('APP_TIMEZONE', 'Asia/Beirut'),
        'language' =>  env('APP_LANGUAGE', 'en'),
        'sourceLanguage' =>  "en",
        'aliases' => subconfig('aliases'),
        'controllerNamespace' => tier()->namespace,
        'defaultRoute' => 'default/index',
    ];
};
