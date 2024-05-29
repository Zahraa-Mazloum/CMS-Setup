<?php

namespace app\tiers\admin\api\controllers;

use app\components\inherits\rest\ActiveController;
use app\models\Tag;
use yii\base\DynamicModel;
use yii\data\ActiveDataFilter;

class TagController extends ActiveController
{
    public $modelClass = Tag::class;

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['dataFilter'] = [
            'class' => ActiveDataFilter::class,
            'searchModel' => function () {
                return (new DynamicModel(['name' => null]))
                    ->addRule(['name'], 'trim')
                    ->addRule(['name'], 'string');
            },
        ];

        return $actions;
    }
}
