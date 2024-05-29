<?php

namespace app\twig\filters;

use app\lib\TwigFilter;

class Pad extends TwigFilter
{

    public function __construct(
        public string $value,
        public int $length,
        public string $padding = " ",
        public string $type = 'right'
    ) {
    }

    public static function name(): string
    {
        return 'pad';
    }

    public function execute()
    {
        return str_pad($this->value, $this->length, $this->padding, match($this->type){
            'left' => STR_PAD_LEFT,
            'both' => STR_PAD_BOTH,
            'right' => STR_PAD_RIGHT,
        });
    }
}
