<?php

use app\components\Cms;
use app\components\UploadManager;
use app\twig\Extension;
use Twig\Extra\Intl\IntlExtension;

return function () {
    $config = [];
    $config['cms'] = [
        'class' => Cms::class,
    ];
    $config['mailer'] = [
        'class' => \yii\symfonymailer\Mailer::class,
        'viewPath' => '@resources/mail',
        'useFileTransport' => envBool('MAILER_USE_FILE_TRANSPORT'),
        'fileTransportPath' => '@runtime/mail',
        'transport' => [
            'dsn' => env('MAILER_USE_TRANSPORT'),
        ],
    ];
    $config['db'] = [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_NAME'),
        'username' => env('DB_USER'),
        'password' => env('DB_PASS'),
        'tablePrefix' => env('DB_PREFIX'),
        'charset' => 'utf8',
        'on afterOpen' => function ($event) {
            $event->sender->createCommand("SET sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';")->execute();
        },
    ];
    $config['i18n'] = [
        'translations' => [
            'app*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@resources/messages',
                'fileMap' => [
                    'app' => 'app.php',
                ],
            ],
        ],
    ];
    $config['uploadManager'] = [
        'class' => UploadManager::class,
    ];
    if (tier()->through === 'http') {
        $config['request'] = [
            'baseUrl' => tier()->path,
            'cookieValidationKey' => env('COOKIE_VALIDATION_KEY'),
            'csrfParam' => '_csrf-' . env('APP_ID', 'app') . tier()->id,
        ];
        $config['user'] = [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-' . env('APP_ID', 'app') . tier()->id, 'httpOnly' => true, 'expire' => time() + YII_EXPIRATION],
        ];
        $config['session'] = [
            'name' => '_session-' . env('APP_ID', 'app') . tier()->id,
            'cookieParams' => ['lifetime' => time() + YII_EXPIRATION],
            'timeout' => time() + YII_EXPIRATION,
        ];
        $config['urlManager'] = [
            'baseUrl' => tier()->path,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => subconfig('rules'),
        ];
    }
    if (tier()->target === 'web') {
        $config['view'] = [
            'renderers' => [
                'twig' => [
                    'class' => 'yii\twig\ViewRenderer',
                    'cachePath' => '@runtime/Twig/cache',
                    'options' => [
                        'auto_reload' => true,
                    ],
                    'globals' => [
                        'html' => ['class' => '\yii\helpers\Html'],
                        'htmlPurifier' => ['class' => '\yii\helpers\HtmlPurifier'],
                        'url' => ['class' => '\yii\helpers\Url'],
                        'json' => ['class' => '\yii\helpers\Json'],
                        'inflector' => ['class' => '\yii\helpers\Inflector'],
                        'markdown' => ['class' => '\yii\helpers\Markdown'],
                        'varDumper' => ['class' => '\yii\helpers\VarDumper'],
                        'file' => ['class' => '\yii\helpers\FileHelper'],
                        'string' => ['class' => '\yii\helpers\StringHelper'],
                        'ip' => ['class' => '\yii\helpers\IpHelper'],
                        'formatConverter' => ['class' => '\yii\helpers\FormatConverter'],
                    ],
                    'uses' => ['yii\bootstrap'],
                    'extensions' => [
                        new IntlExtension(),
                        new Extension(),
                    ],
                ],
            ],
        ];
    }
    return $config;
};
