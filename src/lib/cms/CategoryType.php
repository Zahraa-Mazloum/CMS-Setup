<?php

namespace app\lib\cms;

class CategoryType
{
    private Location $_location;

    public function __construct()
    {
        $this->_location = new Location("local", "settings");
    }

    public function local(): FieldCollection | null
    {
        return $this->_location->at("local");
    }

    public function settings(): FieldCollection | null
    {
        return $this->_location->at("settings");
    }

    public function toArray(): array
    {
        return [
            'attrs' => $this->_location->toArray(),
        ];
    }

    public function __invoke(): array
    {
        return $this->toArray();
    }
}
