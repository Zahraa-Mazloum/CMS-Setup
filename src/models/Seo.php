<?php

namespace app\models;

use app\components\AttrBehavior;
use Yii;

/**
 * This is the model class for table "{{%seo}}".
 *
 * @property int $id
 * @property string|null $target
 * @property string|null $title
 * @property string|null $description
 * @property string|null $keywords
 * @property int|null $is_default
 * @property int $created_at
 * @property int $updated_at
 */
class Seo extends \app\components\inherits\db\ActiveRecord
{
    public function behaviors()
    {
        return [...parent::behaviors(), ...[
            [
                'class' => AttrBehavior::class,
                'propertyName' => 'keywords',
            ],
        ]];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%seo}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'keywords'], 'string'],
            [['is_default'], 'integer'],
            [['target', 'title'], 'string', 'max' => 255],
            [['target'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'target' => 'Target',
            'title' => 'Title',
            'description' => 'Description',
            'keywords' => 'Keywords',
            'is_default' => 'Is Default',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\queries\SeoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\queries\SeoQuery(get_called_class());
    }
}
