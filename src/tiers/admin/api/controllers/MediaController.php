<?php

namespace app\tiers\admin\api\controllers;

use app\components\inherits\rest\SController;
use Exception;

class MediaController extends SController
{
    public function actionUploadImage()
    {
        try{
            return uploadManager()->image(post('section', 'images'), post('base64'));
        }catch(Exception $e){
            error($e->getMessage(), $e->getTrace());
        }
    }

    public function actionUploadFile()
    {
        try{
            return uploadManager()->file(post('section', 'files'), post('name'), post('base64'));
        }catch(Exception $e){
            error($e->getMessage(), $e->getTrace());
        }
    }
}
