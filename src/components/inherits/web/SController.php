<?php

namespace app\components\inherits\web;

use app\components\inherits\web\Controller;

class SController extends Controller
{
    public function beforeAction($action)
    {
        if (isGuest()) {
            $this->redirect(['/default/login']);
            return false;
        }
        return parent::beforeAction($action);
    }
}
