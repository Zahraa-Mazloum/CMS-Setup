<?php

use app\components\AppAsset;
use app\components\Cms;
use app\components\CronJob;
use app\components\DataImporter;
use app\components\Keep;
use app\components\PushNotification;
use app\components\TmpTransfer;
use app\components\UploadManager;
use hisorange\BrowserDetect\Parser as Browser;
use yii\db\ActiveQuery;
use app\forms\LoginForm;
use app\lib\Jwt;
use app\models\Notification;
use app\models\Seo;
use app\models\Session;
use app\models\User;
use yii\helpers\Console;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\queue\Queue;
use yii\web\View;

function inMaintenanceMode()
{
    return YII_MAINTENANCE;
}

function baseUrl()
{
    return Url::base(true);
}

function baseAssetUrl()
{
    if (tier()->path == "/") {
        return baseUrl();
    }
    return str_replace(tier()->path, "", baseUrl());
}

function createUrl($path, $args = [], $basePath = null)
{
    if (!$basePath) {
        $basePath = tier()->path;
    }
    $path = (array)$path;
    if ($args !== []) {
        $path = array_merge($path, $args);
    }
    Url::$urlManager = urlManager();
    Url::$urlManager->setBaseUrl($basePath);
    $url = Url::to($path, true);
    if (tier()->path == "/") {
        return $url;
    }
    return str_replace(tier()->path, "", $url);
}

function appName(): string
{
    return app()->name;
}

function cms(): Cms
{
    return app()->cms;
}


function isJson(string $string): bool
{
    if (is_string($string)) {
        try {
            json_decode($string);
        } catch (\Exception $e) {
            return false;
        }

        return JSON_ERROR_NONE === json_last_error();
    }

    return false;
}

function sendEmail(string|array $receiver, string $title, string $view, array $params = [], $sender = 'support'): void
{
    mailer()->compose([
        'text' => '@resources/mail/' . $view . '/text.php',
        'html' => '@resources/mail/' . $view . '/html.php',
    ], $params)
        ->setTo($receiver)
        ->setFrom([param($sender . 'Email') => param($sender . 'Name')])
        ->setSubject($title)
        ->send();
}

function devEmail(string $title, string $message): void
{
    Yii::$app->mailer->compose()
        ->setFrom(Yii::$app->params['senderEmail'])
        ->setTo(['jawdat@codendot.com', 'jawdat.sobh@gmail.com', 'jawdat.sobh@hotmail.com'])
        ->setSubject($title)
        ->setHtmlBody($message)
        ->send();
}

function createJwtToken(?User $user): ?array
{
    if (!$user && isAuth()) {
        $user = identity();
    }
    if ($user) {
        $token = new Jwt();
        return $token->create([
            'uid' => $user->id,
            'auth_key' => $user->generateAuthKey(),
            'type' => $user->type,
        ]);
    }
    return null;
}

function preLogin(string $tier)
{
    $model = new LoginForm();
    $model->username = post('username');
    $model->password = post('password');
    $model->tier = $tier;
    if ($model->validate()) {
        return createJwtToken($model->user);
    }
    error('Login Error', $model->getErrors());
}

function login(string | array $redirect)
{
    $token = new Jwt();
    $key = post('token');
    $redirectUrl = post('redirectUrl', Url::to($redirect));
    if ($token->validate($key)) {
        $value = $token->get('auth_key');
        $session = Session::find()->where(['token' => $value])->one();
        if ($session && user()->login($session->user, 3600 * 24 * param('jwt.expiration.days', 30))) {
            session()->set('tier', tier()->id);
            session()->set('session_id', $session->id);
            session()->set('token', $key);
            flash("success", "Welcome " . identity()->profile?->name ?? identity()->username);
            return $redirectUrl;
        }
    }
    return null;
}

function logout()
{
    $token = new Jwt();
    $key = session()->get('token');
    if (is_string($key) && $token->validate($key)) {
        $value = $token->get('auth_key');
        Session::logout(identity(), $value);
    }
    session()->remove('token');
    session()->remove('session_id');
    session()->remove('tier');
    user()->logout();
}

function queue(): Queue
{
    return app()->queue;
}

function shortName(mixed $obj): string
{
    return (new \ReflectionClass($obj))->getShortName();
}

function assetUrl(string $path): string
{
    $publishedPath = assetManager()->publish($path, ['forceCopy' => true]);

    return isset($publishedPath[1]) ? Url::to($publishedPath[1], true) : null;
}

function styleTag($path)
{
    $publishedPath = assetManager()->publish($path);
    $styleUrl = Url::to($publishedPath[1], true);

    return '<link href="' . $styleUrl . '" rel="stylesheet">';
}

function sql(ActiveQuery $query)
{
    return $query->prepare(db()->queryBuilder)->createCommand()->rawSql;
}

function output(mixed $data, int $depth = 10, bool $highlight = true)
{
    return '<pre>' . VarDumper::dumpAsString($data, $depth, $highlight) . '</pre>';
}

function twigPaths(): array
{
    return [
        'resources' => '@Resources/twig',
    ];
}

function error($title, $details = [], $send = true, $code = 500)
{
    $debug = debug_backtrace();
    $list = [];
    foreach ($debug as $item) {
        if (isset($item['file'], $item['line'])) {
            $list[] = [
                'file' => $item['file'],
                'line' => $item['line'],
            ];
        }
    }
    if (isConsole()) {
        $screenSize = Console::getScreenSize();
        if (is_array($screenSize) && !empty($screenSize)) {
            echo str_repeat('#', $screenSize[0]) . "\n";
        }
        echo '# ' . Console::ansiFormat($title, [Console::BOLD, Console::FG_RED]) . "\n";
        if (is_array($details) && !empty($details)) {
            echo '# ' . Console::ansiFormat('Error Details: ', [Console::FG_YELLOW]) . "\n";
            foreach ($details as $i => $item) {
                if (!is_int($i)) {
                    echo '# ' . Console::ansiFormat($i, [Console::FG_BLUE]) . ": \n";
                }
                echo '# ' . Console::ansiFormat((is_array($item) ? ' -> ' . implode(', ', $item) : $item), [Console::FG_RED]) . "\n";
            }
        }
        if (!empty($list) && is_array($list)) {
            echo '# ' . Console::ansiFormat('Error Trace: ', [Console::FG_YELLOW]) . "\n";
            foreach ($list as $item) {
                echo '# ' . Console::ansiFormat('[line ' . $item['line'] . ']', [Console::FG_GREEN]) . Console::ansiFormat(' - ', [Console::FG_YELLOW]) . Console::ansiFormat($item['file'], [Console::FG_GREY]) . "\n";
            }
        }
        if (is_array($screenSize) && !empty($screenSize)) {
            echo str_repeat('#', $screenSize[0]) . "\n\n\n";
        }
    } else {
        $response = response();
        $response->statusCode = $code;
        $response->statusText = $title;
        $response->content = Json::encode(['title' => $title, 'details' => $details]);
        $response->format = 'json';
        if ($send) {
            $response->send();
            exit(1);
        }
    }
}

/**
 * Decrypt data from a CryptoJS json encoding string.
 *
 * @param mixed $passphrase
 * @param mixed $jsonString
 */
function cryptoJsAesDecrypt($passphrase, $jsonString): mixed
{
    $jsondata = json_decode($jsonString, true);
    $salt = hex2bin($jsondata['s']);
    $ct = base64_decode($jsondata['ct']);
    $iv = hex2bin($jsondata['iv']);
    $concatedPassphrase = $passphrase . $salt;
    $md5 = [];
    $md5[0] = md5($concatedPassphrase, true);
    $result = $md5[0];
    for ($i = 1; $i < 3; ++$i) {
        $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
        $result .= $md5[$i];
    }
    $key = substr($result, 0, 32);
    $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);

    return json_decode($data, true);
}

/**
 * Encrypt value to a cryptojs compatiable json encoding string.
 *
 * @param mixed $passphrase
 * @param mixed $value
 *
 * @return string
 */
function cryptoJsAesEncrypt($passphrase, $value): string|false
{
    $salt = openssl_random_pseudo_bytes(8);
    $salted = '';
    $dx = '';
    while (strlen($salted) < 48) {
        $dx = md5($dx . $passphrase . $salt, true);
        $salted .= $dx;
    }
    $key = substr($salted, 0, 32);
    $iv = substr($salted, 32, 16);
    $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
    $data = ['ct' => base64_encode($encrypted_data), 'iv' => bin2hex($iv), 's' => bin2hex($salt)];

    return json_encode($data);
}

function crudRules(string $controller): array
{
    return [
        'class' => 'yii\rest\UrlRule',
        'controller' => $controller,
        'tokens' => [
            '{id}' => '<id:\\w+>',
            '{count}' => '<count:\\w+>',
        ],
        'pluralize' => true,
        'prefix' => tier()['type'],
        'extraPatterns' => [
            'POST' => 'create',
            'PUT {id}' => 'update',
            'PATCH {id}' => 'update',
            'DELETE {id}' => 'delete',
            'GET {id}' => 'view',
            'GET {count}' => 'index',
        ],
    ];
}

function debugFile($name, $content, $extension = 'txt')
{
    $dir = alias('@runtime');
    $names = array_filter(explode('/', $name));
    $path = [];
    if (count($names) > 1) {
        for ($i = 0; $i < count($names) - 1; ++$i) {
            $name = Inflector::slug($names[$i]);
            $path[] = $name;
            $dir = $dir . DIRECTORY_SEPARATOR . $name;
            if (!is_dir($dir)) {
                mkdir($dir, 0777);
            }
        }
    }
    $name = Inflector::slug(end($names));
    $path[] = $name . '.' . $extension;
    if (is_array($content)) {
        $content = Json::encode($content, JSON_PRETTY_PRINT);
    }
    if (file_put_contents($dir . DIRECTORY_SEPARATOR . $name . '.' . $extension, $content)) {
        return implode(DIRECTORY_SEPARATOR, $path);
    }

    return null;
}

function php()
{
    ob_start();
    phpinfo();
    $phpinfo = ob_get_contents();
    ob_end_clean();

    return preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $phpinfo);
}

function userAgent(): string
{
    return Browser::userAgent();
}

function isMobile(): bool
{
    return Browser::isMobile();
}

function isTablet(): bool
{
    return Browser::isTablet();
}

function isDesktop(): bool
{
    return Browser::isDesktop();
}

function isBot(): bool
{
    return Browser::isBot();
}

function browserName(): string
{
    return Browser::browserName();
}

function browserFamily(): string
{
    return Browser::browserFamily();
}

function browserVersion(): string
{
    return Browser::browserVersion();
}

function browserVersionMajor(): int
{
    return Browser::browserVersionMajor();
}

function browserVersionMinor(): int
{
    return Browser::browserVersionMinor();
}

function browserVersionPatch(): int
{
    return Browser::browserVersionPatch();
}

function browserEngine(): string
{
    return Browser::browserEngine();
}

function platformName(): string
{
    return Browser::platformName();
}

function platformFamily(): string
{
    return Browser::platformFamily();
}

function platformVersion(): int
{
    return Browser::platformVersion();
}

function platformVersionMajor(): int
{
    return Browser::platformVersionMajor();
}

function platformVersionMinor(): int
{
    return Browser::platformVersionMinor();
}

function platformVersionPatch(): int
{
    return Browser::platformVersionPatch();
}

function isWindows(): bool
{
    return Browser::isWindows();
}

function isLinux(): bool
{
    return Browser::isLinux();
}

function isMac(): bool
{
    return Browser::isMac();
}

function isAndroid(): bool
{
    return Browser::isAndroid();
}

function deviceFamily(): string
{
    return Browser::deviceFamily();
}

function deviceModel(): string
{
    return Browser::deviceModel();
}

function mobileGrade(): string
{
    return Browser::mobileGrade();
}

function isChrome(): bool
{
    return Browser::isChrome();
}

function isFirefox(): bool
{
    return Browser::isFirefox();
}

function isOpera(): bool
{
    return Browser::isOpera();
}

function isSafari(): bool
{
    return Browser::isSafari();
}

function isIE(): bool
{
    return Browser::isIE();
}

function isIEVersion(int $version, string $operator = '='): bool
{
    return Browser::isIEVersion($version, $operator);
}

function isEdge(): bool
{
    return Browser::isEdge();
}

function isInApp(): bool
{
    return Browser::isInApp();
}

function cliOut(...$lines)
{
    if (php_sapi_name() == 'cli') {
        foreach ($lines as $line) {
            echo print_r($line, true) . "\n";
        }
    }
}

function ensureDirExist($dir)
{
    $names = explode(\DIRECTORY_SEPARATOR, str_replace(['/', '\\'], \DIRECTORY_SEPARATOR, str_replace('@public', '', $dir)));
    $path = alias('@public');
    foreach ($names as $name) {
        $path .= \DIRECTORY_SEPARATOR . $name;
        if (!is_dir($path)) {
            mkdir($path, 0777);
        }
    }
    return $path;
}

function sizeFormat($bytes)
{
    if ($bytes < 1024) {
        return $bytes . ' B';
    } elseif ($bytes < 1048576) {
        return round($bytes / 1024, 2) . ' KB';
    } elseif ($bytes < 1073741824) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes < 1099511627776) {
        return round($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes < 1125899906842624) {
        return round($bytes / 1099511627776, 2) . ' TB';
    } elseif ($bytes < 1152921504606846976) {
        return round($bytes / 1125899906842624, 2) . ' PB';
    } elseif ($bytes < 1180591620717411303424) {
        return round($bytes / 1152921504606846976, 2) . ' EB';
    } elseif ($bytes < 1208925819614629174706176) {
        return round($bytes / 1180591620717411303424, 2) . ' ZB';
    } else {
        return round($bytes / 1208925819614629174706176, 2) . ' YB';
    }
}

function nestedLoop($callback, $items, $key = 'items')
{
    foreach ($items as $item) {
        $callback($item);
        if (isset($item[$key]) && !empty($item[$key]) && is_array($item[$key])) {
            nestedLoop($callback, $item[$key], $key);
        }
    }
}

function spell($value, string $language = "en")
{
    if ($value > 0) {
        $f = new NumberFormatter($language, NumberFormatter::SPELLOUT);
        return Inflector::titleize(str_replace('-', ' ', $f->format($value)), true);
    }
    return "";
}

// function pushNotification(): PushNotification
// {
//     return app()->pushNotification;
// }

// function sendNotification($channels, $title, $text, $target, $data = [], $sendNow = false): bool
// {
//     return Notification::send($channels, $title, $text, $target, $data, $sendNow);
// }

function downloadBase64(string $name, string $data, string $contentType = "application/force-download")
{
    header('Content-Description: File Transfer');
    header('Content-Type: ' . $contentType);
    header('Content-Disposition: attachment; filename="' . $name . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . strlen($data));
    echo $data;
    exit;
}

function coverage()
{
    $route = str_replace('/', '-', route());
    $dir = alias("@runtime/coverage");
    if (!is_dir($dir)) {
        mkdir($dir, 0777);
    }
    $file = "$dir/$route.css";
    if (!is_file($file)) {
        fopen($file, "w");
    }
    $css = file_get_contents($file);
    view()->registerCss($css, [
        'id' => 'coverage',
    ]);
}

function deleteFile($str)
{

    // Check for files
    if (is_file($str)) {

        // If it is file then remove by
        // using unlink function
        return unlink($str);
    }

    // If it is a directory.
    elseif (is_dir($str)) {

        // Get the list of the files in this
        // directory
        $scan = glob(rtrim($str, '/') . '/*');

        // Loop through the list of files
        foreach ($scan as $index => $path) {

            // Call recursive function
            deleteFile($path);
        }

        // Remove the directory itself
        return @rmdir($str);
    }
}

function addLog(string $name, array | string $data)
{
    $ext = "txt";
    if (is_array($data)) {
        $data = Json::encode($data, JSON_PRETTY_PRINT);
    }
    $dir = "";

    $folders = [alias("@runtime/logs"), app()->id, date("Y"), date("m-M"), date("d")];
    foreach ($folders as $i => $folder) {
        $dir .= ($i > 0 ? "/" : "") . $folder;
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
    }

    $filename = "logs_" . date("H") . ".$ext";
    $content = is_file("$dir/$filename") && is_readable("$dir/$filename") ? file_get_contents("$dir/$filename") : "";
    if (!$content) {
        $content = "";
    }
    $time = date("Y-m-d H:i:s");
    $ip = userIp();
    $content .= "$time | $ip | $name\n";
    $content .= "$data\n";
    $content .= str_repeat("-", 100) . "\n";

    file_put_contents("$dir/$filename", $content);
}

function languageName($language = null)
{
    if (!$language) {
        $language = language();
    }

    if (!class_exists('Locale')) {
        throw new Exception('You need to install php_intl extension.');
    }

    return Locale::getDisplayLanguage($language);
}

function seo($target, $title, $description, $keywords = [], $isDefault = false)
{
    $model = Seo::find()->where(['target' => $target])->one();
    if (!$model) {
        $model = new Seo();
        $model->target = $target;
        $model->title = $title;
        $model->description = $description;
        $model->keywords = $keywords;
        $model->is_default = intval($isDefault);
        if (!$model->save()) {
            print_r($model->errors);
        }
    }

    view()->on(View::EVENT_BEGIN_PAGE, function ($event) use ($model) {
        $view = $event->sender;

        $view->registerMetaTag([
            'name' => 'title',
            'content' => $model->title,
        ]);

        $view->registerMetaTag([
            'name' => 'description',
            'content' => $model->description,
        ]);

        $view->registerMetaTag([
            'name' => 'keywords',
            'content' => implode(',', $model->keywords),
        ]);
    });
}

function uploadManager(): UploadManager
{
    return app()->uploadManager;
}
