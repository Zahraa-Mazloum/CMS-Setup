<?php

namespace app\components\inherits\web;

class Controller extends \yii\web\Controller
{
    public $layout = '@resources/views/layouts/base.twig';

    public function init()
    {
        parent::init();
    }
}
