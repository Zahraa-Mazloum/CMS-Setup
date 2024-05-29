<?php

namespace app\twig\filters;

use app\lib\TwigFilter;

class Cookies extends TwigFilter
{

    public function __construct(
        public string $name,
        public string $defaultValue = ""
    ) {
    }

    public static function name(): string
    {
        return 'cookies';
    }

    public function execute()
    {
        return $_COOKIE[$this->name] ?? $this->defaultValue;
    }
}
