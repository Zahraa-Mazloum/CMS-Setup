<?php

namespace app\components\inherits\rest;

use app\lib\JwtHttpBearerAuth;

class SController extends Controller
{

    protected $optional = [];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'optional' => $this->optional,
        ];

        return $behaviors;
    }
}
