<?php

namespace app\twig\filters;

use app\lib\TwigFilter;
use yii\helpers\Json as BaseJson;

class Json extends TwigFilter
{

    public function __construct(
        public $content,
        public bool $pretty = false
    ) {
    }

    public static function name(): string
    {
        return 'json';
    }

    public function execute()
    {
        return BaseJson::encode($this->content, $this->pretty && JSON_PRETTY_PRINT);
    }
}
