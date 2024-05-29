<?php 

namespace app\twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Extension extends AbstractExtension {

    public function getFilters()
    {
        return require_once alias('@src/twig/_filters.php');
    }

    public function getTokenParsers()
    {
        return array_map(fn ($cls) => new $cls(), require_once alias('@src/twig/_tags.php'));
    }

    public function getFunctions()
    {
        return array_map(function($function){
            $f = new \ReflectionFunction($function);
            return new TwigFunction($f->name, $f->getClosure());
        }, get_defined_functions(true)['user']);
    }
}
