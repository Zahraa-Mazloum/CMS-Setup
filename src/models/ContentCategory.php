<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%content_category}}".
 *
 * @property int $id
 * @property int|null $content_id
 * @property int|null $category_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Category $category
 * @property Content $content
 */
class ContentCategory extends \app\components\inherits\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%content_category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content_id', 'category_id'], 'integer'],
            [['content_id'], 'exist', 'skipOnError' => true, 'targetClass' => Content::class, 'targetAttribute' => ['content_id' => 'id']],
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
            'content_id' => 'Content ID',
            'category_id' => 'Category ID',
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
     * @return \yii\db\ActiveQuery
     */
    public function getContent()
    {
        return $this->hasOne(Content::class, ['id' => 'content_id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\queries\ContentCategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\queries\ContentCategoryQuery(get_called_class());
    }
}
