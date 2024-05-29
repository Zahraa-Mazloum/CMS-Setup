<?php

namespace app\twig\tags;

use Twig\Node\Node;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;

class ContentNode extends Node
{

    public function __construct(Node $body, $attributes = [], int $lineno = 0, string $tag = null)
    {
        parent::__construct(['body' => $body], $attributes, $lineno, $tag);
    }
    
    public function compile(Compiler $compiler)
    {
        $compileExpression = function (Compiler $compiler, mixed $expression) {
            if ($expression instanceof AbstractExpression) {
                $compiler->subcompile($expression);
            } else {
                $compiler->repr($expression);
            }
        };
        
        $compiler
            ->addDebugInfo($this)
            ->write("\$context['widget'] = \app\widgets\Content::begin(['id' => ");
            $compileExpression($compiler, $this->getAttribute('id'));
            $compiler->write("]);")
            ->write("\$context['content'] = \$context['widget']->model;")
            ->write("\$context['html'] = fn() => \$context['widget']->html;") 
            ->indent()
            ->subcompile($this->getNode('body'))
            ->outdent()
            ->write("\app\widgets\Content::end();\n");
    }
}
