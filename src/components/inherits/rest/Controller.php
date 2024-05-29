<?php

namespace app\components\inherits\rest;


class Controller extends \yii\rest\Controller
{
    public $serializer = [
        'class' => Serializer::class,
    ];

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
