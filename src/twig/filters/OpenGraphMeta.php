<?php

namespace app\twig\filters;

use app\lib\TwigFilter;

class OpenGraphMeta extends TwigFilter
{

    public function __construct(
        public string $name,
        public string $value,
    ) {
    }

    public static function name(): string
    {
        return 'og';
    }

    public function execute()
    {
        view()->registerMetaTag([
            'property' => $this->name,
            'content' => $this->value,
        ]);
        return null;
    }
}
