<?php

use app\lib\core\Tier;
use app\models\Admin;
use app\models\User as ModelsUser;
use yii\base\Application as BaseApplication;
use yii\base\Module;
use yii\web\Application as WebApplication;
use yii\console\Application as ConsoleApplication;
use yii\web\Request as WebRequest;
use yii\console\Request as ConsoleRequest;
use yii\base\Response as BaseResponse;
use yii\web\Response as WebResponse;
use yii\console\Response as ConsoleResponse;
use yii\base\Controller as BaseController;
use yii\console\Controller as ConsoleController;
use yii\base\Action as BaseAction;
use yii\base\ErrorHandler as BaseErrorHandler;
use yii\web\ErrorHandler as WebErrorHandler;
use yii\console\ErrorHandler as ConsoleErrorHandler;
use yii\base\Security;
use yii\base\View as BaseView;
use yii\caching\CacheInterface;
use yii\web\View as WebView;
use yii\db\Connection;
use yii\i18n\Formatter;
use yii\i18n\I18N;
use yii\mail\MailerInterface;
use yii\rbac\ManagerInterface;
use yii\web\AssetManager;
use yii\web\Session;
use yii\web\UrlManager;
use yii\web\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

function app(): BaseApplication|WebApplication|ConsoleApplication
{
    return Yii::$app;
}

function request(): WebRequest|ConsoleRequest
{
    return app()->request;
}

function get(string $name = null, mixed $defaultValue = null)
{
    return request()->get($name, $defaultValue);
}

function post(string $name = null, mixed $defaultValue = null)
{
    if (!$name) {
        return request()->post();
    }
    return ArrayHelper::getValue(request()->post(), $name, $defaultValue);
}

function isAjax(): bool
{
    return request()->isAjax;
}

function isPjax(): bool
{
    return request()->isPjax;
}

function isPost(): bool
{
    return request()->isPost;
}

function isGet(): bool
{
    return request()->isGet;
}

function isDelete(): bool
{
    return request()->isDelete;
}

function isPut(): bool
{
    return request()->isPut;
}

function isOptions(): bool
{
    return request()->isOptions;
}

function isHead(): bool
{
    return request()->isHead;
}

function isPatch(): bool
{
    return request()->isPatch;
}

function isSecureConnection(): bool
{
    return request()->isSecureConnection;
}

function isConsoleRequest(): bool
{
    return request()->isConsoleRequest;
}

function isFlash(): bool
{
    return request()->isFlash;
}

function response(): BaseResponse|WebResponse|ConsoleResponse
{
    return app()->response;
}

function controller(): BaseController|ConsoleController
{
    return app()->controller;
}

function module(): Module|null
{
    return controller()->module;
}

function action(): BaseAction
{
    return controller()->action;
}

function view(): BaseView|WebView
{
    return app()->view;
}

function db($name = 'db'): Connection
{
    return app()->{$name};
}

function assetManager(): AssetManager
{
    return app()->assetManager;
}

function authManager(): ManagerInterface
{
    return app()->authManager;
}

function basePath(): string
{
    return app()->basePath;
}

function cache(): CacheInterface
{
    return app()->cache;
}

function container(): array
{
    return app()->container;
}

function errorHandler(): BaseErrorHandler|ConsoleErrorHandler|WebErrorHandler
{
    return app()->errorHandler;
}

function formatter(): Formatter
{
    return app()->formatter;
}

function i18n(): I18N
{
    return app()->i18n;
}

function mailer(): MailerInterface
{
    return app()->mailer;
}

function runtimePath(): string
{
    return app()->runtimePath;
}

function security(): Security
{
    return app()->security;
}

function timeZone(): string
{
    return app()->timeZone;
}

function uniqueId(): string
{
    return app()->uniqueId;
}

function urlManager(): UrlManager
{
    return app()->urlManager;
}

function vendorPath(): string
{
    return app()->vendorPath;
}

function session(): Session
{
    return app()->session;
}

function t(string $category, string $message, array $params = [], mixed $language = null): string
{
    return Yii::t($category, $message, $params, $language);
}

function setAlias(string $name, string $path): void
{
    Yii::setAlias($name, $path);
}

function alias(string $name, bool $throwException = true): string
{
    return Yii::getAlias($name, $throwException);
}

function languages(): array
{
    return param('languages', []);
}

function language(): string
{
    return app()->language;
}

function route(): string
{
    return controller()->route;
}

function envName(): string
{
    return YII_ENV;
}

function readyScript($script): void
{
    view()->registerJs($script, WebView::POS_READY);
}

function script($script, $position = WebView::POS_END): void
{
    view()->registerJs($script, $position);
}

function user(): User
{
    return app()->user;
}

function identity(): ModelsUser|null
{
    return user()->identity;
}

function profile(): Admin|null
{
    return identity()?->profile ?? null;
}

function isGuest(): bool
{
    return user()->isGuest;
}

function isAuth(): bool
{
    return !isGuest();
}

function params(): array
{
    return app()->params;
}

function param($name, $defaultValue = null): mixed
{
    return ArrayHelper::getValue(params(), $name, $defaultValue);
}

function setParam(string $name, mixed $value)
{
    ArrayHelper::setValue(app()->params, $name, $value);
}

function port(): int|null
{
    return request()->serverPort;
}

function hostName(): string
{
    return ('443' === request()->serverPort ? 'https://' : 'http://') . request()->serverName;
}

function domainName(): string
{
    return request()->serverName;
}

function userIp(): string|null
{
    return request()->getUserIP();
}

function userHost(): string|null
{
    return request()->getUserHost();
}

function flash($type, $message): void
{
    session()->setFlash($type, $message);
}

function flashes(): array
{
    return session()->getAllFlashes();
}

function url(string $route, array $params = []): string
{
    return Url::to([$route, ...$params]);
}

function createObject($type, $params = []): object
{
    return Yii::createObject($type, $params);
}

function tier(): Tier
{
    return YII_TIER;
}
