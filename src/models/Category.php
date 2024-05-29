<?php

namespace app\models;

use app\components\AttrBehavior;
use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "{{%category}}".
 *
 * @property int $id
 * @property string|null $type
 * @property int|null $parent_id
 * @property string|null $path
 * @property string|null $status
 * @property int|null $position
 * @property string|null $attrs
 * @property int $created_at
 * @property int $updated_at
 *
 * @property CategoryLocal[] $locals
 * @property CategoryLocal $local
 * @property ContentCategory[] $contentCategories
 * @property Content[] $contents
 * @property Category $parent
 * @property Category[] $children
 * @property Category[] $parents
 * 
 * @property string $url
 * @property string $typeUrl
 */
class Category extends \app\components\inherits\db\ActiveRecord
{

    static $joins = [];

    public function behaviors()
    {
        return [...parent::behaviors(), ...[
            [
                'class' => AttrBehavior::class,
                'propertyName' => 'attrs',
            ]
        ]];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'position'], 'integer'],
            [['status', 'attrs'], 'string'],
            [['type', 'path'], 'string', 'max' => 255],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['parent_id' => 'id']],
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
            'parent_id' => 'Parent ID',
            'path' => 'Path',
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
    public function getLocals()
    {
        return $this->hasMany(CategoryLocal::class, ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocal($language = null)
    {
        if (!$language) {
            $language = language();
        }
        return $this->hasOne(CategoryLocal::class, ['category_id' => 'id'])->where(['language' => $language]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentCategories()
    {
        return $this->hasMany(ContentCategory::class, ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContents()
    {
        return $this->hasMany(Content::class, ['id' => 'content_id'])->via('contentCategories');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::class, ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Category::class, ['parent_id' => 'id']);
    }

    /**
     * @return array
     */
    public function getParents()
    {
        return Category::find()->where(new Expression('FIND_IN_SET(`id`, :path)', [':path' => $this->path]))->orderBy(new Expression('FIELD(`id`, ' . $this->path . ')'))->all();
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return createUrl('/default/category', ['id' => $this->id], "/");
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
     * @return \app\queries\CategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new \app\queries\CategoryQuery(get_called_class());
        if (!empty(static::$joins) && is_array(static::$joins)) {
            $query->from(['category' => static::tableName()]);
            $query->joinWith(static::$joins);
            $query->groupBy('category.id');
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
        return ['local', 'locals', 'contents', 'parent', 'children', 'parents'];
    }
}
