<?php

use app\lib\core\Tier;
use app\lib\JsFile;
use Dotenv\Dotenv;
use yii\base\Exception;
use yii\helpers\Json;

function createApp(string $dir)
{
    $dotenv = Dotenv::createImmutable($dir, '.env');
    $dotenv->safeLoad();

    defined('YII_DEBUG') or
        define(
            'YII_DEBUG',
            strtolower(env('YII_DEBUG', 'on')) == 'on'
        );
    defined('YII_ENV') or
        define(
            'YII_ENV',
            env('YII_ENV', 'dev')
        );
    defined('YII_MAINTENANCE') or
        define(
            'YII_MAINTENANCE',
            env('YII_MAINTENANCE', 'off') == 'on'
        );

    defined('YII_EXPIRATION') or define('YII_EXPIRATION', (env("JWT_EXPIRATION_DAYS", 30) * 24 * 60 * 60));

    if (YII_ENV === 'dev') {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    require_once $dir . '/vendor/yiisoft/yii2/Yii.php';

    if (php_sapi_name() === 'cli') {
        $tier = new Tier([
            'id' => 'console',
            'path' => '',
            'target' => 'console',
            'through' => 'console',
            'namespace' => 'app\\commands',
            'root' => $dir,
        ]);
        $exitCode = $tier->run();
        exit($exitCode);
    }

    $path = $_SERVER['REQUEST_URI'];
    $tiers = subconfig('tiers');
    $tierConfig = [];
    foreach ($tiers as $id => $data) {
        if (str_starts_with($path, $id)) {
            $tierConfig = [
                ...$data,
                'path' => $id,
                'through' => 'http',
                'root' => $dir,
            ];
            break;
        }
    }

    if (empty($tierConfig)) {
        throw new Exception("Wrong Url", 404);
    }
    $tier = new Tier($tierConfig);
    $tier->run();
}



function env(string $name, $defaultValue = null): mixed
{
    return $_ENV[$name] ?? getenv($name) ?? $defaultValue;
}

function envBool(string $name, bool $defaultValue = false): bool
{
    $value = env($name, $defaultValue);
    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
}

function subconfig(string $name): array
{
    return (require dirname(dirname(__DIR__)) . "/config/_$name.php")();
}

function fileVersion(string $path)
{
    $filename = alias($path);
    if (is_file($filename)) {
        return filemtime($filename);
    }

    return null;
}

function appJsFile($name)
{
    $mode = envName();
    $version = 0;
    $fileChunkName = resolveJsFileName($mode, tier()->id, $name.'.js');
    $filename = alias('@public/'.$mode.'/'.tier()->id.'/'.$fileChunkName);
    if(is_string($filename) && is_file($filename)) {
        $version = filemtime($filename);
        view()->registerJsFile('./'.$mode.'/'.tier()->id.'/'.$fileChunkName.'?v='.$version, [
            'async' => true, 'defer' => true, 'type' => 'module',
        ], $name);
    }
}

function resolveJsFileName(string $mode, string $tier, string $filename, $isMainFile = false)
{
    return JsFile::resolveName($mode, $tier, $filename, $isMainFile);
}

function initJavascriptData(Tier $tier)
{
    response()->headers->set('Content-Type', 'application/javascript');
    $fct = include alias('@config/_js.php');
    $rand = rand(1000, 5000);
    $values = str_split(base64_encode(json_encode($fct($tier))));
    $data = Json::encode(array_reduce($values, function ($list, $value) use ($rand) {
        $list[] = $rand - ord($value);
        return $list;
    }, [$rand]));

    return <<< JS
        window.zxcvbnm = {$data};
    JS;
}

function debug($content)
{
    return '<pre class="pre-debug" style="white-space: pre-wrap;padding: 15px; background: #FFEB3B; color: #212121; ">' . print_r($content, true) . '</pre>';
}

function isConsole(): bool
{
    return 'cli' == php_sapi_name();
}
