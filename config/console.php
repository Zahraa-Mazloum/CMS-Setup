<?php

$config = [
    ...subconfig('meta'),
    'bootstrap' => subconfig('bootstrap'),
    'modules' => subconfig('modules'),
    'components' => subconfig('components'),
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => null,
            'templateFile' => '@app/generators/migration.php',
            'migrationNamespaces' => [
                'app\migrations',
                'yii\queue\db\migrations',
            ],
        ],
    ],
    'params' => subconfig('params'),
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [
            'model' => [
                'class' => 'yii\gii\generators\model\Generator',
                'templates' => [
                    'jawdat_model' => '@app/generators/model',
                ],
            ],
        ],
    ];
}

return $config;
