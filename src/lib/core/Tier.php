<?php

namespace app\lib\core;

use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

class Tier extends BaseObject
{

    public $id;
    public $path;
    public $target;
    public $through;
    public $namespace;
    public $api = null;
    public $root;

    public function run(): int
    {
        defined("YII_TIER") or define("YII_TIER", $this);
        if ($this->id === "console") {
            defined("YII_TARGET") or define("YII_TARGET", "console");
            return $this->runConsole();
        }
        defined("YII_TARGET") or define("YII_TARGET", "web");
        return $this->runWeb();
    }

    protected function runConsole(): int
    {
        $config = ArrayHelper::merge($this->getConfig('console'), $this->getConfig("tiers/_" . $this->id));
        $application = new \yii\console\Application($config);
        return $application->run();
    }

    protected function runWeb(): int
    {
        if ($this->target == "api") {
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Max-Age: 0'); // 86400 : cache for 1 day

                header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Pragma: no-cache");

                header('Access-Control-Expose-Headers: X-Pagination-Current-Page, X-Pagination-Total-Count, X-Pagination-Per-Page, X-Pagination-Page-Count, X-FirePHP-Version');
            }

            // Access-Control headers are received during OPTIONS requests
            if ('OPTIONS' == $_SERVER['REQUEST_METHOD']) {
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
                }

                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
                }

                exit(0);
            }
        }

        $config = ArrayHelper::merge($this->getConfig('main'), $this->getConfig("tiers/_" . $this->id));
        // echo '<pre>';
        // print_r($config);
        // echo '</pre>';
        // exit;
        $application = new \yii\web\Application($config);

        return $application->run();
    }

    protected function getConfig(string $configName)
    {
        return require $this->root . '/config/' . $configName . '.php';
    }
}
