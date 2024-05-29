<?php

namespace app\components\inherits\base;

use ReflectionClass;
use yii\base\Widget as BaseWidget;

class Widget extends BaseWidget
{

    public function getViewPath()
    {
        return alias("@resources/widgets");
    }
}
