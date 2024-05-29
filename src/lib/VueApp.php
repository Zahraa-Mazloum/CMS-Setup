<?php

namespace app\lib;

use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\web\View;

class VueApp
{
    public static $scriptDef = false;

    protected function __construct(
        public string $name,
        public string $id,
        public ?string $tier = null,
        public array $params = [],
        public bool | string $loader = true
    ) {
    }

    public function start()
    {
        $this->createScript();

        return $this->createVue();
    }

    protected function createScript()
    {
        $name = strtolower(Inflector::id2camel($this->name, '-'));
        $id = $this->id;
        $params = [];
        if (!empty($this->params) && is_array($this->params)) {
            foreach ($this->params as $prop => $var) {
                $params[] = '' . $prop . ':' . Json::encode($var);
            }
        }
        $paramsJson = '{' . implode(',', $params) . '}';
        $initScript = <<< JS
            document.addEventListener("readystatechange", async (event) => {
                if (event.target.readyState === 'complete') {
                    if(window.hasOwnProperty('codendot') && window.codendot.hasOwnProperty('vueApps') && window.codendot.vueApps.hasOwnProperty('{$name}')){
                        await window.codendot.vueApps['{$name}']('{$id}', {$paramsJson})
                    }
                }
            })
        JS;
        script($initScript, View::POS_HEAD);
    }

    protected function createVue()
    {
        $id = $this->id ?? $this->name;
        appJsFile(strtolower(Inflector::id2camel($this->name, '-')));

        $loader = $this->loader;
        $loaderColor = 'white';
        if ($loader && !is_bool($loader)) {
            $loaderColor = $loader;
        }

        return div(
            $loader ? div(
                div(
                    span(
                        'Loading...',
                        [
                            'class' => 'visually-hidden',
                        ]
                    ),
                    [
                        'class' => 'spinner-border',
                        'role' => 'status',
                    ]
                ),
                [
                    'class' => ['text-center', 'vue-loader', "text-$loaderColor"],
                ]
            ) : '',
            [
                'id' => $id,
                'data' => [
                    'name' => $this->name,
                ],
            ]
        );
    }

    public static function create(string $name, string $id = null, string $tier = null, array $params = [], bool $loader = true)
    {
        if (!$id) {
            $id = $name;
        }
        $app = new static($name, $id, $tier, $params, $loader);
        return $app->start();
    }
}
