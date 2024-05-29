<?php

namespace app\twig\filters;

use app\lib\TwigFilter;

class At extends TwigFilter
{
    public function __construct(
        public ?array $data,
        public int | string $key,
        public ?string $defaultValue = null,
    ) {
    }

    public static function name(): string
    {
        return 'at';
    }

    public function execute()
    {
        return is_array($this->data) && array_key_exists($this->key, $this->data) ? $this->data[$this->key] : $this->defaultValue;
    }
}
