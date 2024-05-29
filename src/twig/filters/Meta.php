<?php

namespace app\twig\filters;

use app\lib\TwigFilter;

class Meta extends TwigFilter
{

    public function __construct(
        public string $name,
        public string $value,
        public string $key = "name",
    ) {
    }

    public static function name(): string
    {
        return 'meta';
    }

    public function execute()
    {
        view()->registerMetaTag([
            $this->key => $this->name,
            'content' => $this->value,
        ]);
        return null;
    }
}
