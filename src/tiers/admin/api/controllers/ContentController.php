<?php

namespace app\tiers\admin\api\controllers;

use app\components\inherits\rest\ActiveController;
use app\enums\ContentStatus;
use app\models\Content;
use app\models\ContentCategory;
use app\models\ContentLocal;
use yii\base\DynamicModel;
use yii\data\ActiveDataFilter;

class ContentController extends ActiveController
{
    public $modelClass = Content::class;

    public function beforeAction($action)
    {
        if ($action->id == 'index') {
            Content::$joins = [
                'locals' => function ($query) {
                    $query->from(['locals' => ContentLocal::tableName()]);
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

    protected function afterSave(Content $content)
    {
        $locals = post('locals');
        foreach ($locals as $local) {
            $model = ContentLocal::find()->where(['content_id' => $content->id, 'language' => $local['language']])->one();
            if (!$model) {
                $model = new ContentLocal();
                $model->content_id = $content->id;
                $model->language = $local['language'];
            }
            $model->title = $local['title'];
            $model->excerpt = $local['excerpt'];
            $model->blocks = $local['blocks'] ?? [];
            $model->plain_text = blocksToText($model->blocks);
            $model->attrs = $local['attrs'] ?? [];
            if (!$model->save()) {
                error('Failed to save content language ' . $local['language'], $model->getErrors());
            }
        }
        $content->saveManyToMany(ContentCategory::class, 'content_id', $content->id, 'category_id', post('categories'));
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['dataFilter'] = [
            'class' => ActiveDataFilter::class,
            'searchModel' => function () {
                $fields = (new $this->modelClass())->attributes;
                return (new DynamicModel($fields))
                    ->addRule(['id'], 'safe')
                    ->addRule(['title', 'excerpt', 'type', 'blocks', 'status'], 'trim')
                    ->addRule(['title', 'excerpt', 'type', 'blocks', 'status'], 'string');
            },
        ];

        return $actions;
    }

    public function actionSoftDelete($id)
    {
        return Content::updateAll(['status' => ContentStatus::DELETED->value], ['id' => $id]);
    }
}
