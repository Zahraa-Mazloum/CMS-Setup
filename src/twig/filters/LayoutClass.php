<?php

namespace app\twig\filters;

use app\lib\TwigFilter;

class LayoutClass extends TwigFilter
{

    public function __construct(
        public string | array $className,
    ) {
    }

    public static function name(): string
    {
        return 'layoutClass';
    }

    public function execute()
    {
        view()->blocks['layoutClass'] = is_array($this->className) ? implode(' ', $this->className) : $this->className;
        return null;
    }
}
