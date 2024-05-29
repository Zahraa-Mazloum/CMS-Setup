<?php

namespace app\components;

use yii\base\Behavior;
use yii\helpers\Json;

class AttrBehavior extends Behavior
{
    public $propertyName = 'attrs';

    public function __get($name)
    {
        if (is_array($this->owner->{$this->propertyName}) && in_array($name, array_keys($this->owner->{$this->propertyName}))) {
            return $this->owner->{$this->propertyName}[$name];
        }

        return $this->owner->__get($name);
    }

    public function canGetProperty($name, $checkVars = true)
    {
        if (is_array($this->owner->{$this->propertyName}) && in_array($name, array_keys($this->owner->{$this->propertyName}))) {
            return true;
        }

        return parent::canGetProperty($name, $checkVars);
    }

    public function events()
    {
        return [
            ($this->owner::class)::EVENT_BEFORE_VALIDATE => function ($event) {
                $this->attrsToSave();
            },
            ($this->owner::class)::EVENT_BEFORE_INSERT => function ($event) {
                $this->attrsToSave();
            },
            ($this->owner::class)::EVENT_BEFORE_UPDATE => function ($event) {
                $this->attrsToSave();
            },
            ($this->owner::class)::EVENT_AFTER_FIND => function ($event) {
                $this->attrsToView();
            },
            ($this->owner::class)::EVENT_AFTER_INSERT => function ($event) {
                $this->attrsToView();
            },
            ($this->owner::class)::EVENT_AFTER_UPDATE => function ($event) {
                $this->attrsToView();
            },
        ];
    }

    public function attrsToSave()
    {
        if ($this->owner->hasProperty($this->propertyName) && !$this->owner->{$this->propertyName}) {
            $this->owner->{$this->propertyName} = [];
        }
        if ($this->owner->hasProperty($this->propertyName) && is_array($this->owner->{$this->propertyName})) {
            $this->owner->{$this->propertyName} = Json::encode($this->owner->{$this->propertyName});
        }
    }

    public function attrsToView()
    {
        if ($this->owner->hasProperty($this->propertyName) && $this->owner->{$this->propertyName} && isJson($this->owner->{$this->propertyName})) {
            $this->owner->{$this->propertyName} = Json::decode($this->owner->{$this->propertyName});
        }
    }
}
