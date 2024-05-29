<?php

namespace app\lib\cms;

class SettingCategory
{

    private string $_label;
    private array $_fields = [];

    public function __construct(protected string $_name)
    {
        $this->_label = $_name;
    }

    public function label(string $value): self
    {
        $this->_label = $value;
        return $this;
    }

    public function field(string $name): Field
    {
        return $this->_fields[$name] = new Field($name);
    }

    public function toArray()
    {
        return [
            'name' => $this->_name,
            'label' => $this->_label,
            'items' => array_map(function (Field $field) {
                return $field->toArray();
            }, array_values($this->_fields))
        ];
    }
}
