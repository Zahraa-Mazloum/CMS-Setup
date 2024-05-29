<?php

namespace app\twig\filters;

use app\lib\TwigFilter;
use Twig\Markup;

class Nl2Br extends TwigFilter
{

    public function __construct(
        public $content
    ) {
    }

    public static function name(): string
    {
        return 'nl2br';
    }

    public function execute()
    {
        return new Markup(str_replace("\\n","<br>",nl2br(htmlentities(is_string($this->content)?$this->content:'', ENT_QUOTES, 'UTF-8'))), 'UTF-8');
    }
}
