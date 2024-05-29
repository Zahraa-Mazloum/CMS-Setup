<?php

namespace app\twig\filters;

use app\lib\TwigFilter;

class BodyClass extends TwigFilter
{

    public function __construct(
        public string | array $className,
    ) {
    }

    public static function name(): string
    {
        return 'bodyClass';
    }

    public function execute()
    {
        view()->blocks['bodyClass'] = is_array($this->className) ? implode(' ', $this->className) : $this->className;
        return null;
    }
}
