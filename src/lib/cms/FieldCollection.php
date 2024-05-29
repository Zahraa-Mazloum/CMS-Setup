<?php

namespace app\lib\cms;

use yii\base\Arrayable;

class FieldCollection
{

    private array $_fields = [];

    public function __construct(private string $_name)
    {
    }

    public function field(string $name): Field
    {
        return $this->_fields[$name] = new Field($name);
    }

    public function toArray()
    {
        return [
            'column' => $this->_name,
            'fields' => array_map(function (Field $field) {
                return $field->toArray();
            }, array_values($this->_fields))
        ];
    }
}
