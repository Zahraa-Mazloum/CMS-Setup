<?php

namespace app\twig\filters;

use app\lib\TwigFilter;

class Call extends TwigFilter
{

    public function __construct(
        public $className,
        public string $methodName,
        public array $methodParams = [],
        public mixed $defaultValue = null,
    ) {
    }

    public static function name(): string
    {
        return 'call';
    }

    public function execute()
    {
        if (class_exists($this->className) && method_exists($this->className, $this->methodName)) {
            return $this->className::{$this->methodName}(...$this->methodParams);
        }
        return $this->defaultValue;
    }
}
