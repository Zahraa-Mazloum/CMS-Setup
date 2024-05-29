<?php

namespace app\twig\filters;

use app\lib\TwigFilter;

class BodyStyle extends TwigFilter
{

    public function __construct(
        public string | array $styles,
    ) {
    }

    public static function name(): string
    {
        return 'bodyStyle';
    }

    public function execute()
    {
        view()->blocks['bodyStyle'] = is_array($this->styles) ? implode('', array_map(fn ($name, $value) => "$name:$value;", array_keys($this->styles), array_values($this->styles))) : $this->styles;
        return null;
    }
}
