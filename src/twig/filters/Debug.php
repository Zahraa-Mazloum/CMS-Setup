<?php

namespace app\twig\filters;

use app\lib\TwigFilter;
use Twig\Markup;

class Debug extends TwigFilter
{
    public function __construct(
        public $content,
    ) {
    }

    public static function name(): string
    {
        return 'debug';
    }

    public function execute()
    {
        return new Markup(debug($this->content), 'UTF-8');
    }
}
