<?php

namespace app\tiers\admin\api\controllers;

use app\components\inherits\rest\ActiveController;
use app\models\Category;
use app\models\CategoryLocal;
use yii\base\DynamicModel;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class CategoryController extends ActiveController
{
    public $modelClass = Category::class;

    public function beforeAction($action)
    {
        if ($action->id == 'index') {
            Category::$joins = [
                'locals' => function ($query) {
                    $query->from(['locals' => CategoryLocal::tableName()]);
                }
            ];
        } else if (in_array($action->id, ['create', 'update'])) {
            $this->modelClass::listen([
                $this->modelClass::EVENT_AFTER_INSERT => function ($event) {
                    $this->afterSave($event->sender);
                },
                $this->modelClass::EVENT_AFTER_UPDATE => function ($event) {
                    $this->afterSave($event->sender);
                },
            ]);
        }
        return parent::beforeAction($action);
    }

    protected function afterSave($category)
    {
        $locals = post('locals');
        foreach ($locals as $local) {
            $model = CategoryLocal::find()->where(['category_id' => $category->id, 'language' => $local['language']])->one();
            if (!$model) {
                $model = new CategoryLocal();
                $model->category_id = $category->id;
                $model->language = $local['language'];
            }
            $model->title = $local['title'];
            $model->excerpt = $local['excerpt'];
            $model->attrs = $local['attrs'] ?? [];
            if (!$model->save()) {
                error('Failed to save category language ' . $local['language'], $model->getErrors());
            }
        }
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['dataFilter'] = [
            'class' => ActiveDataFilter::class,
            'searchModel' => function () {
                $fields = (new $this->modelClass())->attributes;
                return (new DynamicModel($fields))
                    ->addRule(['id', 'parent_id'], 'safe')
                    ->addRule(['title', 'excerpt', 'type'], 'trim')
                    ->addRule(['title', 'excerpt', 'type'], 'string');
            },
        ];
        $actions['index']['prepareDataProvider'] = function ($action, $filter) {

            $requestParams = request()->getBodyParams();
            if (empty($requestParams)) {
                $requestParams = request()->getQueryParams();
            }

            $query = $this->modelClass::find();
            if (!empty($filter)) {
                if (ArrayHelper::isIndexed($filter)) {
                    foreach ($filter as &$item) {
                        if (is_array($item) && array_key_exists('parent_id', $item) && strtolower($item['parent_id']) == 'null') {
                            $item['parent_id'] = null;
                        }
                    }
                } else if (array_key_exists('parent_id', $filter) && strtolower($filter['parent_id']) == 'null') {
                    $filter['parent_id'] = null;
                }
                $query->andWhere($filter);
            }

            return createObject([
                'class' => ActiveDataProvider::class,
                'query' => $query,
                'pagination' => false,
                'sort' => [
                    'params' => $requestParams,
                ],
            ]);
        };

        return $actions;
    }
}
