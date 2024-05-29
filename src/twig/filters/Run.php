<?php

namespace app\twig\filters;

use app\lib\TwigFilter;

class Run extends TwigFilter
{

    public function __construct(
        public $callback,
        public array $callbackParams = [],
        public mixed $defaultValue = null,
    ) {
    }

    public static function name(): string
    {
        return 'run';
    }

    public function execute()
    {
        $fn = $this->callback;
        if (is_callable($fn)) {
            return $fn(...$this->callbackParams);
        }
        return $this->defaultValue;
    }
}
