<?php

namespace app\twig\filters;

use app\lib\TwigFilter;
use Twig\Markup;
use app\lib\VueApp;

class Vue extends TwigFilter
{
    public function __construct(
        public string $name,
        public array $props = [],
        public  bool $loader = true,
    ) {
    }

    public static function name(): string
    {
        return 'vue';
    }

    public function execute()
    {
        $tier = tier()->id;
        $app = $this->name;

        $dir = explode('/', $this->name);
        if (count($dir) == 2) {
            $tier = $dir[0];
            $app = $dir[1];
        }
        $names = explode('#', $app);
        $name = $names[0];
        $id = $names[1] ?? $names[0];
        if (!$id) {
            $id = $name;
        }
        return new Markup(VueApp::create($name, $id, $tier, $this->props, $this->loader), 'UTF-8');
    }
}
