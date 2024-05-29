<?php

namespace app\twig\filters;

use app\lib\TwigFilter;
use yii\base\Model;
use Twig\Markup;
use yii\helpers\Html;

class FormErrors extends TwigFilter
{

    public function __construct(
        public Model $model,
    ) {
    }

    public static function name(): string
    {
        return 'errors';
    }

    public function execute()
    {
        if ($this->model->hasErrors()) {
            $options['class'] = 'errors-summary';
            return new Markup(Html::errorSummary($this->model, $options), 'UTF-8');
        }
        return null;
    }
}
