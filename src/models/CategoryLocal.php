<?php

namespace app\models;

use app\components\AttrBehavior;
use Yii;

/**
 * This is the model class for table "{{%category_local}}".
 *
 * @property int $id
 * @property int|null $category_id
 * @property string|null $language
 * @property string|null $title
 * @property string|null $excerpt
 * @property string|null $attrs
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Category $category
 */
class CategoryLocal extends \app\components\inherits\db\ActiveRecord
{

    public function behaviors()
    {
        return [...parent::behaviors(), ...[
            [
                'class' => AttrBehavior::class,
                'propertyName' => 'attrs',
            ],
        ]];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%category_local}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id'], 'integer'],
            [['excerpt', 'attrs'], 'string'],
            [['language', 'title'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category ID',
            'language' => 'Language',
            'title' => 'Title',
            'excerpt' => 'Excerpt',
            'attrs' => 'Attrs',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\queries\CategoryLocalQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\queries\CategoryLocalQuery(get_called_class());
    }
}
