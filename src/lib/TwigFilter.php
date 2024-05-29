<?php

namespace app\lib;

use Twig\TwigFilter as BaseTwigFilter;

abstract class TwigFilter
{

    private static $_filters = [];

    abstract public static function name(): string;

    abstract public function execute();

    public static function assign(string $filterClass)
    {
        static::$_filters[] = new BaseTwigFilter($filterClass::name(), fn (...$params) => (new $filterClass(...$params))->execute());
    }

    public static function filters()
    {
        return static::$_filters;

    }
}
