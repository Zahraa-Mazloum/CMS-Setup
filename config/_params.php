<?php

return function () {
    return [
        'jwt.key.secret' => env('JWT_KEY_SECRET'),
        'jwt.expiration.days' => env('JWT_EXPIRATION_DAYS', 30),
        'expire' => YII_EXPIRATION,
        'cms_type' => require __DIR__ . '/cms/type.php',
        'languages' => array_map(fn($code) => ['value' => $code, 'label' => languageName($code)], explode(",",env("APP_LANGUAGES"))),
        'senderEmail' => env('EMAIL_SYSTEM_RECEIVE'),
        'senderName' => env('EMAIL_SENDER_NAME'),
    ];
};
