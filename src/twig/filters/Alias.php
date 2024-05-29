<?php

namespace app\twig\filters;

use app\lib\TwigFilter;

class Alias extends TwigFilter
{

    public function __construct(
        public string $path,
    ) {
    }

    public static function name(): string
    {
        return 'alias';
    }

    public function execute()
    {
        return alias($this->path);
    }
}
