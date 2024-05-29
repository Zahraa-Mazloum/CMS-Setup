<?php

namespace app\twig\filters;

use app\lib\TwigFilter;

class Title extends TwigFilter
{

    public function __construct(
        public string $title,
        public bool $return = false
    ) {
    }

    public static function name(): string
    {
        return 'title';
    }

    public function execute()
    {
        view()->title = $this->title;
        if ($this->return)
            return $this->title;
        return null;
    }
}
