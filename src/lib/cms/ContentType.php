<?php

namespace app\lib\cms;

class ContentType
{
    private bool $_linked = false;
    private int $_max = 0;
    private Location $_location;

    private static $_instance = null;

    public function __construct()
    {
        $this->_location = new Location("local", "1", "2", "3");
    }

    public function linked(): self
    {
        $this->_linked = true;
        return $this;
    }

    public function max(int $value): self
    {
        $this->_max = $value;
        return $this;
    }

    public function local(): FieldCollection | null
    {
        return $this->_location->at("local");
    }

    public function col1(): FieldCollection | null
    {
        return $this->_location->at("1");
    }

    public function col2(): FieldCollection | null
    {
        return $this->_location->at("2");
    }

    public function col3(): FieldCollection | null
    {
        return $this->_location->at("3");
    }

    public function repeater(string $property): FieldCollection | null
    {
        return $this->_location->at("repeater_$property");
    }

    public function toArray(): array
    {
        return [
            'linked' => $this->_linked,
            'max' => $this->_max,
            'attrs' => $this->_location->toArray(),
        ];
    }

    public function __invoke(): array
    {
        return $this->toArray();
    }
}
