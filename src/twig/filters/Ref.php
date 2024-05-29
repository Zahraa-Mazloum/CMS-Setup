<?php

namespace app\twig\filters;

use app\lib\TwigFilter;
use app\models\Setting as ModelsSetting;

class Ref extends TwigFilter
{
    public function __construct(
        public string $name,
        public ?string $defaultValue = null,
    ) {
    }

    public static function name(): string
    {
        return 'ref';
    }

    public function execute()
    {
        return ModelsSetting::reference($this->name, $this->defaultValue)?->value ?? $this->defaultValue;
    }
}
