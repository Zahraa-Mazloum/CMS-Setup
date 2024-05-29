<?php

use app\lib\core\Tier;
use yii\helpers\Url;

return function (Tier $tier) {
    $admin = [];
    if ($tier->id === "admin") {
        $admin['cms'] = (require __DIR__ . '/_cms.php')();
        $admin['settings'] = require __DIR__ . '/settings/settings.php';
    }
    $messagesfile = alias("@resources/messages/".language()."/app.php");
    $messages = [];
    if(is_file($messagesfile)){
        $messages = require $messagesfile;
    }
    return [
        'id' => $tier->id,
        'name' => app()->name,
        'tier' => $tier->id,
        'env' => envName(),
        'language' => language(),
        'languages' => languages(),
        'user' => app()->has('user') ? identity() : null,
        'token' => app()->has('user') ? session()->get('token') : null,
        'isGuest' => isGuest(),
        'isAuth' => isAuth(),
        'route' => route(),
        'url' => [
            'self' =>  Url::base(true),
            'api' =>  Url::base(true) . $tier->api,
            'public' => baseAssetUrl(),
            'base' => [
                'absolute' => Url::base(true),
                'relative' => Url::base(),
            ],
        ],
        'translations' => $messages,
        'permissions' => [],
        'isMobile' => isMobile(),
        'isTablet' => isTablet(),
        'isDesktop' => isDesktop(),
        // 'settings' => Setting::fetch(),
        'firebase' => [
            'apiKey' => env('FIREBASE_API_KEY'),
            'authDomain' => env('FIREBASE_AUTH_DOMAIN'),
            'projectId' => env('FIREBASE_PROJECT_ID'),
            'storageBucket' => env('FIREBASE_STORAGE_BUCKET'),
            'messagingSenderId' => env('FIREBASE_MESSAGING_SENDER_ID'),
            'appId' => env('FIREBASE_APP_ID'),
            'measurementId' => env('FIREBASE_MEASUREMENT_ID'),
            'vapidKey' => env('FIREBASE_VAPID_KEY'),
        ],
        'recaptchaSiteKey' => env('RECAPTCHA_SITE_KEY'),
        ...$admin,
    ];
};
