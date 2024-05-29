<?php

namespace app\twig\filters;

use app\lib\TwigFilter;
use Twig\Markup;
use yii\helpers\Html;
use yii\helpers\Url;

class Link extends TwigFilter
{

    public function __construct(
        public string $title,
        public string $route,
        public array $params = [],
        public array $options = []
    ) {
        $this->options['encode'] = false;
        if (Url::to([$route, ...$params]) == Url::current()) {
            $this->options['class'] = ($this->options['class'] ?? "") . " active";
        }
    }

    public static function name(): string
    {
        return 'link';
    }

    public function execute()
    {
        return new Markup(Html::a($this->title, [$this->route, ...$this->params], $this->options), 'UTF-8');
    }
}
