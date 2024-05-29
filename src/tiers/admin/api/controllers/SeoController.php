<?php

namespace app\tiers\admin\api\controllers;

use app\components\inherits\rest\ActiveController;
use app\models\Seo;
use yii\base\DynamicModel;
use yii\data\ActiveDataFilter;

class SeoController extends ActiveController
{
    public $modelClass = Seo::class;

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['dataFilter'] = [
            'class' => ActiveDataFilter::class,
            'searchModel' => function () {
                $fields = (new $this->modelClass())->attributes;
                return (new DynamicModel($fields))
                    ->addRule(['id', 'keywords'], 'safe')
                    ->addRule(['target', 'title', 'description'], 'trim')
                    ->addRule(['target', 'title', 'description'], 'string');
            },
        ];
        return $actions;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        /*checkActionAccess('index', 'seo');
        checkActionAccess('create', 'seo.add');
        checkActionAccess('update', 'seo.edit');
        checkActionAccess('view', 'seo.view');
        checkActionAccess('delete', 'seo.delete');*/
    }
}
