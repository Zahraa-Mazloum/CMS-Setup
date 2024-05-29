<?php

namespace app\models;

use app\components\AttrBehavior;

/**
 * This is the model class for table "{{%friendly_url}}".
 *
 * @property int $id
 * @property string|null $target
 * @property string|null $name
 * @property string|null $pattern
 * @property string|null $route
 * @property string|null $params
 * @property int $created_at
 * @property int $updated_at
 */
class FriendlyUrl extends \app\components\inherits\db\ActiveRecord
{
    public function behaviors()
    {
        return [...parent::behaviors(), ...[
            [
                'class' => AttrBehavior::class,
                'propertyName' => 'params',
            ],
        ]];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%friendly_url}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['params'], 'string'],
            [['target', 'name', 'pattern', 'route'], 'string', 'max' => 255],
            [['target'], 'unique'],
            [['pattern'], 'unique'],
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
            'name' => 'Name',
            'pattern' => 'Pattern',
            'route' => 'Route',
            'params' => 'Params',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\queries\FriendlyUrlQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\queries\FriendlyUrlQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        static::generateRules();
    }

    public static function generateRules()
    {
        $models = static::find()->all();
        $rules = [];
        foreach ($models as $model) {
            $route = $model->route;
            $pattern = $model->pattern;
            $params = implode(PHP_EOL, array_map(function ($name, $value) {
                return <<< EOL
                "{$name}" => "{$value}",
                EOL;
            }, array_keys($model->params), array_values($model->params)));
            $rules[] = <<< EOL
            [
                "route" => "{$route}",
                "pattern" => "{$pattern}",
                "defaults" => [
                    {$params}
                ],
            ],
            EOL;
        }
        $rules = implode(PHP_EOL, $rules);
        $date = date('l, d F, Y H:i');
        $rules = <<< EOL
        <?php 

        /** 
         * Auto Generated
         * 
         * @date {$date}
         */

        return [
           {$rules} 
        ];
        EOL;
        file_put_contents(alias("@runtime/friendly-url.php"), $rules);
    }
}
