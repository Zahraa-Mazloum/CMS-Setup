<?php

namespace app\components\inherits\db;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\StringHelper;

class ActiveRecord extends \yii\db\ActiveRecord
{
    private static array $_events = [];

    public function init()
    {
        parent::init();
        foreach (static::$_events[static::class] ?? [] as $name => $handler) {
            $this->on($name, $handler);
        }
    }

    public static function listen(array $events)
    {
        static::$_events[static::class] = $events;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @return string
     */
    public function classNameBase()
    {
        return StringHelper::basename(get_class($this));
    }

    public function constantNames()
    {
        return [];
    }

    public function fields()
    {
        $list = parent::fields();
        unset($list['created_at'], $list['updated_at']);

        return $list;
    }

    public function saveManyToMany($modelClass, $attr, $value, $relatedAttr, $data, $extra = null)
    {
        if (null != $data) {
            $modelClass::deleteAll([$attr => $value]);
            if (!empty($data) && is_array($data)) {
                foreach ($data as $item) {
                    $row = new $modelClass();
                    $row->{$attr} = $value;
                    $row->{$relatedAttr} = $item;
                    if (is_callable($extra)) {
                        $extra($row);
                    }
                    $row->save();
                }
            }
        }
    }

    // public function afterSave($insert, $changedAttributes)
    // {
    //     parent::afterSave($insert, $changedAttributes);
    //     $ignoreClasses = [Log::class];
    //     if (!isConsole() && Yii::$app->has('user') && !isGuest() && !in_array(static::class, $ignoreClasses)) {
    //         $objectName = $this->classNameBase();
    //         $data = [];
    //         if (!empty($changedAttributes) && is_array($changedAttributes)) {
    //             foreach ($changedAttributes as $name => $value) {
    //                 if (null != $value && !in_array($name, ['created_at', 'updated_at']) && $this->hasProperty($name) && $this->{$name} != $value && !is_array($value) && !is_array($this->{$name})  && mb_strlen($this->{$name}) <= 100) {
    //                     $data[] = [
    //                         'property' => $name,
    //                         'old_value' => ($this->hasProperty($name . 'List') && isset($this->{$name . 'List'}[$value])) ? $this->{$name . 'List'}[$value] : $value,
    //                         'new_value' => ($this->hasProperty($name . 'List') && isset($this->{$name . 'List'}[$this->{$name}])) ? $this->{$name . 'List'}[$this->{$name}] : $this->{$name},
    //                     ];
    //                 }
    //             }
    //         }
    //         if ($insert) {
    //             // audit()->db('Create new record of ' . $objectName . ' #' . $this->id, $data);
    //         } else {
    //             if (!empty($data)) {
    //                 // audit()->db('Update record of ' . $objectName . ' #' . $this->id, $data);
    //             }
    //         }
    //     }
    // }
}
