<?php

namespace app\models;

use app\components\AttrBehavior;
use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%content}}".
 *
 * @property int $id
 * @property string|null $type
 * @property string|null $status
 * @property int|null $position
 * @property string|null $attrs
 * @property int $created_at
 * @property int $updated_at
 *
 * @property ContentCategory[] $contentCategories
 * @property Category[] $categories
 * @property ContentLocal[] $locals
 * @property ContentLocal $local
 * 
 * @property string $url
 * @property string $typeUrl
 */
class Content extends \app\components\inherits\db\ActiveRecord
{

    static $joins = [];

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
        return '{{%content}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'attrs'], 'string'],
            [['position'], 'integer'],
            [['type'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'status' => 'Status',
            'position' => 'Position',
            'attrs' => 'Attrs',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentCategories()
    {
        return $this->hasMany(ContentCategory::class, ['content_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])->via('contentCategories');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocals()
    {
        return $this->hasMany(ContentLocal::class, ['content_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocal($language = null)
    {
        if (!$language) {
            $language = language();
        }
        return $this->hasOne(ContentLocal::class, ['content_id' => 'id'])->where(['language' => $language]);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return createUrl('/default/content', ['id' => $this->id], "/");
    }

    /**
     * @return string
     */
    public function getTypeUrl()
    {
        return createUrl('/default/contents', ['type' => $this->type], "/");
    }

    /**
     * {@inheritdoc}
     * @return \app\queries\ContentQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new \app\queries\ContentQuery(get_called_class());
        if (!empty(static::$joins) && is_array(static::$joins)) {
            $query->from(['content' => static::tableName()]);
            $query->joinWith(static::$joins);
            $query->groupBy('content.id');
        }
        return $query;
    }

    public function fields()
    {
        $list = parent::fields();

        $list['url'] = function () {
            return $this->url;
        };

        $list['typeUrl'] = function () {
            return $this->typeUrl;
        };

        return $list;
    }

    public function extraFields()
    {
        return ['local', 'locals', 'categories'];
    }
}
