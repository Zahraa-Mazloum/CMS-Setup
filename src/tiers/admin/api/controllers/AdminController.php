<?php

namespace app\tiers\admin\api\controllers;

use app\components\inherits\rest\ActiveController;
use app\enums\UserType;
use app\models\Admin;
use app\models\User;
use yii\base\DynamicModel;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;

class AdminController extends ActiveController
{

    public $modelClass = Admin::class;

    public function beforeAction($action)
    {
        if (in_array($action->id, ['create', 'update'])) {
            $this->modelClass::listen([
                $this->modelClass::EVENT_BEFORE_INSERT => function ($event) {
                    $this->beforeSave($event->sender);
                },
                $this->modelClass::EVENT_BEFORE_UPDATE => function ($event) {
                    $this->beforeSave($event->sender);
                },
            ]);
        }
        return parent::beforeAction($action);
    }

    protected function beforeSave($model)
    {
        $user = User::findOne($model->id);
        if (!$user) {
            $user = new User();
            $user->type = UserType::ADMIN->value;
        }
        if ($user->load(post(), 'user') && $user->save()) {
            $model->id = $user->id;
        } else {
            error('Invalid Data', $user->errors);
        }
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['dataFilter'] = [
            'class' => ActiveDataFilter::class,
            'searchModel' => function () {
                return (new DynamicModel(['id' => null, 'name' => null, 'username' => null, 'status' => null]))
                    ->addRule(['id'], 'integer')
                    ->addRule(['name', 'username', 'status'], 'trim')
                    ->addRule(['name', 'username', 'status'], 'string');
            },
            'attributeMap' => [
                'id' => '{{admin}}.[[id]]',
                'name' => '{{admin}}.[[name]]',
                'username' => '{{user}}.[[username]]',
                'status' => '{{user}}.[[status]]',
            ],
        ];
        $actions['index']['prepareDataProvider'] = function ($action, $filter) {
            $query = $this->modelClass::find();

            $query->from(['admin' => $this->modelClass::tableName()]);
            $joins = [
                'user' => function ($query) {
                    $query->from(['user' => User::tableName()]);
                },
            ];

            if (!empty($filter)) {
                $query->andWhere($filter);
            }

            $query->joinWith($joins, true, 'INNER JOIN');

            $query->andWhere(['user.type' => UserType::ADMIN->value]);

            $query->groupBy('user.id');

            $requestParams = request()->getBodyParams();
            if (empty($requestParams)) {
                $requestParams = request()->getQueryParams();
            }

            return createObject([
                'class' => ActiveDataProvider::class,
                'query' => $query,
                'pagination' => [
                    'params' => $requestParams,
                ],
                'sort' =>  [
                    'params' => $requestParams,
                ],
            ]);
        };
        return $actions;
    }
}
