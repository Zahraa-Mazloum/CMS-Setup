<?php

namespace app\twig\filters;

use app\lib\TwigFilter;
use yii\helpers\Url as BaseUrl;

class Url extends TwigFilter
{

    public function __construct(
        public string $route,
        public array $params = []
    ) {
    }

    public static function name(): string
    {
        return 'url';
    }

    public function execute()
    {
        return BaseUrl::to([$this->route, ...$this->params], true);
    }
}
