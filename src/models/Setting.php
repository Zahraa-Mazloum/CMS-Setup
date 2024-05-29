<?php

namespace app\models;

use app\enums\SettingType;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%setting}}".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $value
 * @property string|null $type
 * @property int $created_at
 * @property int $updated_at
 */
class Setting extends \app\components\inherits\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%setting}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value', 'type'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'value' => 'Value',
            'type' => 'Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\queries\SettingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\queries\SettingQuery(get_called_class());
    }

    protected static function saveSetting($name, $value, $type, $section = null, $overriteValue = true)
    {
        $model = static::find()->where(['name' => $name, 'type' => $type])->one();
        if (!$model) {
            $model = new static();
            $model->name = $name;
            $model->type = $type;
            $model->value = $value;
        }
        if ($overriteValue) {
            $model->value = $value;
        }
        if ($section !== false) {
            $model->section = $section;
        }
        return $model->save();
    }

    public static function setSystem($name, $value, $section = null, $overriteValue = true)
    {
        return static::saveSetting($name, $value, SettingType::SYSTEM->value, $section, $overriteValue);
    }

    public static function setReference($name, $value)
    {
        return static::saveSetting($name, $value, SettingType::REFERENCE->value);
    }

    public static function setUser($name, $value)
    {
        return static::saveSetting($name, $value, SettingType::USER->value);
    }

    protected static function getSetting($name, $type, $defaultValue = null)
    {
        $model = static::find()->where(['name' => $name, 'type' => $type])->one();
        if (!$model) {
            $model = new static();
            $model->name = $name;
            $model->type = $type;
            $model->value = $defaultValue;
            $model->save();
        }
        return $model;
    }

    public static function system($name, $defaultValue = null)
    {
        return static::getSetting($name, SettingType::SYSTEM->value, $defaultValue);
    }

    public static function reference($name, $defaultValue = null)
    {
        return static::getSetting($name, SettingType::REFERENCE->value, $defaultValue);
    }

    public static function user($name, $defaultValue = null)
    {
        return static::getSetting($name, SettingType::USER->value, $defaultValue);
    }

    public function beforeValidate()
    {
        if (is_iterable($this->value)) {
            $this->value = Json::encode($this->value);
        }

        return parent::beforeValidate();
    }

    public function afterFind()
    {
        parent::afterFind();
        if (is_string($this->value) && isJson($this->value)) {
            $this->value = Json::decode($this->value);
        }
    }
}
