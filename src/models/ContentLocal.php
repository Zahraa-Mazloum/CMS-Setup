<?php

namespace app\models;

use app\components\AttrBehavior;
use Yii;

/**
 * This is the model class for table "{{%content_local}}".
 *
 * @property int $id
 * @property int|null $content_id
 * @property string|null $language
 * @property string|null $title
 * @property string|null $excerpt
 * @property string|null $blocks
 * @property string|null $plain_text
 * @property string|null $attrs
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Content $content
 */
class ContentLocal extends \app\components\inherits\db\ActiveRecord
{

    public function behaviors()
    {
        return [...parent::behaviors(), ...[
            [
                'class' => AttrBehavior::class,
                'propertyName' => 'blocks',
            ],
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
        return '{{%content_local}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content_id'], 'integer'],
            [['excerpt', 'blocks', 'plain_text', 'attrs'], 'string'],
            [['language', 'title'], 'string', 'max' => 255],
            [['content_id'], 'exist', 'skipOnError' => true, 'targetClass' => Content::class, 'targetAttribute' => ['content_id' => 'id']],
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
            'language' => 'Language',
            'title' => 'Title',
            'excerpt' => 'Excerpt',
            'blocks' => 'Blocks',
            'plain_text' => 'Plain Text',
            'attrs' => 'Attrs',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
     * @return \app\queries\ContentLocalQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\queries\ContentLocalQuery(get_called_class());
    }

    public function fields()
    {
        $list = parent::fields();

        unset($list['plain_text']);

        return $list;
    }
}
