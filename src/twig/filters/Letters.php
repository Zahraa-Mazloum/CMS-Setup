<?php

namespace app\twig\filters;

use app\lib\TwigFilter;
use Twig\Markup;

class Letters extends TwigFilter
{

    public function __construct(
        public string $text,
    ) {
    }

    public static function name(): string
    {
        return 'letters';
    }

    public function execute()
    {
        $texts = explode(" ", preg_replace('/\s+/', ' ', trim($this->text)));
        $words = [];
        foreach ($texts as $text) {
            $letters = str_split($text);
            $words[] = implode('', array_map(function ($letter, $i) {
                return '<span class="letter letter-' . $i . '">' . $letter . '</span>';
            }, $letters, array_keys($letters)));
        }
        return new Markup(implode('&nbsp;', $words), 'UTF-8');
    }
}
