<?php

namespace app\widgets;

use app\components\inherits\base\Widget;
use app\enums\ContentStatus;
use app\models\Content as ModelsContent;
use app\models\Setting;

class Content extends Widget
{

    public string | int $id;

    private $_model = null;

    public function init()
    {
        parent::init();

        $id = $this->id;
        if (is_string($id) && str_starts_with($id, "cms.contents.")) {
            $id = Setting::reference($id)->value;
        }
        $this->_model = ModelsContent::find()->where(['id' => $id, 'status' => ContentStatus::ACTIVE->value])->one();
        ob_start();
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function getHtml()
    {
        if (!$this->_model) {
            return null;
        }
        return $this->render("content.twig", ['model' => $this->_model]);
    }

    public function run()
    {
        $content = ob_get_clean();
        return $content;
    }
}
