<?php

namespace app\twig\filters;

use app\lib\TwigFilter;

class Language extends TwigFilter
{
    public function __construct(
        public string $code,
    ) {
    }

    public static function name(): string
    {
        return 'language';
    }

    public function execute()
    {
        return languageName($this->code);
    }
}
