<?php

namespace app\twig\tags;

use Twig\Node\Node;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use yii\helpers\Json;

class CategoriesNode extends Node
{

    public function __construct(Node $body, $attributes = [], int $lineno = 0, string $tag = null)
    {
        parent::__construct(['body' => $body], $attributes, $lineno, $tag);
    }
    
    public function compile(Compiler $compiler)
    {
        $options = [
            "type" => $this->hasAttribute("type") ? $this->getAttribute("type") : null,
            "limit" => $this->hasAttribute("limit") ? $this->getAttribute("limit") : 20,
            "sort" => $this->hasAttribute("sort")?$this->getAttribute("sort") : null,
            "parent" => $this->hasAttribute("parent") ? $this->getAttribute("parent") : null,
        ];

        $compileExpression = function (Compiler $compiler, mixed $expression) {
            if ($expression instanceof AbstractExpression) {
                $compiler->subcompile($expression);
            } else {
                $compiler->repr($expression);
            }
        };

        $compiler->addDebugInfo($this)
            ->write('$query = \app\models\Category::find()->alias("category");');
        $compiler->write('$query->where(["category.status" => \app\enums\CategoryStatus::ACTIVE->value]);');
        $compiler->write('
            $query->joinWith([
                "local" => function($query){
                    $query->alias("local");
                },
            ], true, "INNER JOIN");
        ');

        if ($options["sort"]) {
            $compiler->write('$query->sort(');
            $compileExpression($compiler, $options["sort"]);
            $compiler->write(');');
        }

        if ($options["limit"]) {
            $compiler->write('$query->limit(');
            $compileExpression($compiler, $options["limit"]);
            $compiler->write(');');
        }

        if ($options["type"]) {
            $compiler->write('$query->andWhere(["category.type" => ');
            $compileExpression($compiler, $options["type"]);
            $compiler->write(']);');
        }
        
        if ($options["parent"]) {
            $compiler->write('$query->andWhere(["category.parent_id" => ');
            $compileExpression($compiler, $options["parent"]);
            $compiler->write(']);');
        }else{
            $compiler->write('$query->andWhere(["category.parent_id" => NULL]);');
        }

        $compiler->write('
            $maxQuery = clone $query;
            $maxQuery->select("MAX(content.updated_at)");
        ');

        $compiler->write('
            $dependency = createObject([
                "class" => "yii\caching\DbDependency",
                "sql" => sql($maxQuery),
            ]);
            $query->cache(true, $dependency);
            $context["sql"] = sql($query);
            $context["categories"] = $query->all();
        ');
        $compiler->indent()
            ->subcompile($this->getNode('body'))
            ->outdent()->write("\n");
    }
}
