<?php

namespace app\tiers\admin\api\controllers;

use Exception;
use yii\rest\Controller;

class DefaultController extends Controller
{
    public function actionLogin()
    {
        return preLogin('admin');
    }

    public function actionUploadImage()
    {
        try {
            return uploadManager()->binaryImage("editor", $_FILES['image']);
        } catch (Exception $e) {
            return [
                'success' => 0,
            ];
        }
    }
}
