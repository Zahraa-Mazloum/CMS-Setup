<?php

namespace app\lib\cms;

class Location
{
    private array $_collections = [];

    public function __construct(string ...$names)
    {
        foreach ($names as $name) {
            $this->_collections[$name] = new FieldCollection($name);
        }
    }

    public function at(string $name): FieldCollection | null
    {
        if (!array_key_exists($name, $this->_collections)) {
            $this->_collections[$name] = new FieldCollection($name);
        }
        return $this->_collections[$name];
    }

    public function toArray(): array
    {
        return array_map(function (FieldCollection $collection) {
            return $collection->toArray();
        }, array_values($this->_collections));
    }
}
