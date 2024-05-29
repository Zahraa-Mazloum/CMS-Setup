<?php

namespace app\components\inherits\web;

use Yii;
use yii\web\NotFoundHttpException;

class Request extends \yii\web\Request
{
    public function getBaseUrl()
    {
        return str_replace(
            ['/public'],
            [''],
            parent::getBaseUrl()
        );
    }
}
