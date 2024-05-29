<?php

namespace app\components;

use yii\base\BootstrapInterface;

class bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        if (tier()->target === "web") {
            
            $app->on('beforeAction', function ($event) {
                controller()->layout = '@resources/tiers/' . tier()->id . '/layouts/main.twig';
                controller()->viewPath = '@resources/tiers/' . tier()->id . '/' . controller()->id . '/';
            });
        }
    }
}
