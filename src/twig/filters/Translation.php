<?php

namespace app\twig\filters;

use app\lib\TwigFilter;

class Translation extends TwigFilter
{
    public function __construct(
        public string $message,
        public string $category = 'app',
        public array $params = [],
        public ?string $language = null
    ) {
    }

    public static function name(): string
    {
        return 't';
    }

    public function execute()
    {
        return t($this->category, $this->message, $this->params, $this->language);
    }
}
