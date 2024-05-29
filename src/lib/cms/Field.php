<?php

namespace app\lib\cms;

class Field
{

    protected string $_type = "string";
    protected string $_label;
    protected mixed $_defaultValue = null;
    protected ?string $_icon = null;
    protected array $_props = [];
    protected array $_attributes = [];

    public function __construct(private string $_name)
    {
        $this->_label = $_name;
    }

    public function type(string $value): self
    {
        $this->_type = $value;
        return $this;
    }

    public function label(string $value): self
    {
        $this->_label = $value;
        return $this;
    }

    public function defaultValue(mixed $value): self
    {
        $this->_defaultValue = $value;
        return $this;
    }

    public function prop(string $name, mixed $value): self
    {
        $this->_props[$name] = $value;
        return $this;
    }

    public function attribute(string $name, mixed $value): self
    {
        $this->_attributes[$name] = $value;
        return $this;
    }

    public function icon(string $value): self
    {
        $this->_icon = $value;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->_name,
            'type' => $this->_type,
            'label' => $this->_label,
            'icon' => $this->_icon,
            'defaultValue' => $this->_defaultValue,
            'props' => $this->_resolveProps($this->_props),
            'attributes' => $this->_attributes,
        ];
    }

    private function _resolveProps(array $props)
    {
        if(in_array($this->_type, ['select', 'checkbox', 'radio']) && array_key_exists('options', $props) && is_callable($props['options'])){
            $props['options'] = $props['options']();
        }
        return $props;
    }
}
