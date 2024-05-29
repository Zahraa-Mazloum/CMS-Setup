<?php

namespace app\twig\tags;

use Twig\Node\Node;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use yii\helpers\Json;

class ContentsNode extends Node
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
            "category" => $this->hasAttribute("category") ? $this->getAttribute("category") : null,
        ];

        $compileExpression = function (Compiler $compiler, mixed $expression) {
            if ($expression instanceof AbstractExpression) {
                $compiler->subcompile($expression);
            } else {
                $compiler->repr($expression);
            }
        };

        $compiler->addDebugInfo($this)
            ->write('$query = \app\models\Content::find()->alias("content");');
        $compiler->write('$query->where(["content.status" => \app\enums\ContentStatus::ACTIVE->value]);');
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
            $compiler->write('$query->andWhere(["content.type" => ');
            $compileExpression($compiler, $options["type"]);
            $compiler->write(']);');
        }

        if ($options["category"]) {
            $compiler->write('
                $query->joinWith([
                    "categories" => function($query){
                        $query->alias("categories");
                    },
                ], true, "INNER JOIN");
            ');
            $compiler->write('$query->andWhere(["categories.id" => ');
            $compileExpression($compiler, $options["category"]);
            $compiler->write(']);');
        }

        $compiler->write('
            $maxQuery = clone $query;
        ');

        if ($options["category"]) {
            $compiler->write('
                $maxQuery->select("MAX(content.updated_at,categories.updated_at)");
            ');
        }else{
            $compiler->write('
                $maxQuery->select("MAX(content.updated_at)");
            ');
        }

        $compiler->write('
            $dependency = createObject([
                "class" => "yii\caching\DbDependency",
                "sql" => sql($maxQuery),
            ]);
            $query->cache(true, $dependency);
            $context["sql"] = sql($query);
            $context["contents"] = $query->all();
        ');
        $compiler->indent()
            ->subcompile($this->getNode('body'))
            ->outdent()->write("\n");
    }
}
