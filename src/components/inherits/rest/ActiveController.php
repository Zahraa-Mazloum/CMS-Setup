<?php


namespace app\components\inherits\rest;

use app\lib\JwtHttpBearerAuth;

class ActiveController extends \yii\rest\ActiveController
{
    public $serializer = [
        'class' => Serializer::class,
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
        ];

        return $behaviors;
    }

    public function beforeAction($action)
    {
        if (get('serialize')) {
            $this->serializer = [
                'class' => Serializer::class,
                'collectionEnvelope' => 'items',
                'linksEnvelope' => 'links',
                'metaEnvelope' => 'meta',
            ];
        }
        return parent::beforeAction($action);
    }
}
