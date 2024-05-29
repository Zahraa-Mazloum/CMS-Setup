<?php

namespace app\components;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = './';
    public $css = [];
    public $js = [];
    public $depends = [];

    public function init()
    {
        parent::init();
        $mainSuffix = tier()->id;
        $mode = envName();
        $this->jsOptions = ['async' => true, 'defer' => true, 'type' => 'module',];
        $filename = resolveJsFileName($mode, $mainSuffix, "$mainSuffix.js", true);
        $this->js[] = $mode . '/' . $mainSuffix . '/' . $filename . '?v=' . fileVersion('@webroot/' . $mode . '/' . $mainSuffix . '/' . $filename);
        view()->registerLinkTag([
            // 'rel' => 'preload',
            'rel' => 'stylesheet',
            'href' => './' . $mode . '/' . $mainSuffix . '/main.css?v=' . fileVersion('@webroot/' . $mode . '/' . $mainSuffix . '/main.css'),
            'as' => 'style',
            // 'onload' => "this.onload=null;this.rel='stylesheet';document.getElementById('coverage')?.remove()",
            'id' => 'main-css',
        ]);
        view()->registerLinkTag([
            'rel' => 'preconnect',
            'href' => 'https://www.google.com',
        ]);
        view()->registerLinkTag([
            'rel' => 'preconnect',
            'href' => 'https://www.gstatic.com',
            'crossorigin' => true,
        ]);
    }
}
