<?php

namespace app\twig\tags;

use Twig\Node\Node;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;

class CategoryNode extends Node
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
            ->write("\$context['category'] = \app\models\Category::find()->where(['id' => ");
            $compileExpression($compiler, $this->getAttribute('id'));
            $compiler->write(", 'status' => \app\\enums\CategoryStatus::ACTIVE->value])->one();")
            ->indent()
            ->subcompile($this->getNode('body'))
            ->outdent()
            ->write("\n");
    }
}
