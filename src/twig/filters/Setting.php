<?php

namespace app\twig\filters;

use app\lib\TwigFilter;
use app\models\Setting as ModelsSetting;

class Setting extends TwigFilter
{
    public function __construct(
        public string $name,
        public ?string $defaultValue = null,
    ) {
    }

    public static function name(): string
    {
        return 'setting';
    }

    public function execute()
    {
        return ModelsSetting::system($this->name, $this->defaultValue)?->value ?? $this->defaultValue;
    }
}
