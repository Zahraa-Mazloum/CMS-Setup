<?php

namespace app\tiers\admin\api\controllers;

use app\components\inherits\rest\ActiveController;
use app\models\FriendlyUrl;
use yii\base\DynamicModel;
use yii\data\ActiveDataFilter;

class FriendlyUrlController extends ActiveController
{
    public $modelClass = FriendlyUrl::class;

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['dataFilter'] = [
            'class' => ActiveDataFilter::class,
            'searchModel' => function () {
                $fields = (new $this->modelClass())->attributes;
                return (new DynamicModel($fields))
                    ->addRule(['id'], 'safe')
                    ->addRule(['target', 'name', 'pattern', 'route'], 'trim')
                    ->addRule(['target', 'name', 'pattern', 'route'], 'string');
            },
        ];
        return $actions;
    }
}
