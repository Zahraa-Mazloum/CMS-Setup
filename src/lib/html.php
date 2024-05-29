<?php

use yii\helpers\Html;

function tag(string|bool|null $name, string|array $content = '', array $options = [])
{
    if (is_array($content)) {
        $content = implode(' ', $content);
    }
    return Html::tag($name, $content, $options);
}

function table(mixed $content = '', array $options = [])
{
    return tag(__FUNCTION__, $content, $options);
}

function tr(mixed $content = '', array $options = [])
{
    return tag(__FUNCTION__, $content, $options);
}

function td(mixed $content = '', array $options = [])
{
    return tag(__FUNCTION__, $content, $options);
}

function th(mixed $content = '', array $options = [])
{
    return tag(__FUNCTION__, $content, $options);
}

function thead(mixed $content = '', array $options = [])
{
    return tag(__FUNCTION__, $content, $options);
}

function tbody(mixed $content = '', array $options = [])
{
    return tag(__FUNCTION__, $content, $options);
}

function tfoot(mixed $content = '', array $options = [])
{
    return tag(__FUNCTION__, $content, $options);
}

function p(mixed $content = '', array $options = [])
{
    return tag(__FUNCTION__, $content, $options);
}

function div(mixed $content = '', array $options = [])
{
    return tag(__FUNCTION__, $content, $options);
}

function span(mixed $content = '', array $options = [])
{
    return tag(__FUNCTION__, $content, $options);
}

function a(mixed $content = '', array $options = [])
{
    return tag(__FUNCTION__, $content, $options);
}

function h1(mixed $content = '', array $options = [])
{
    return tag(__FUNCTION__, $content, $options);
}

function h2(mixed $content = '', array $options = [])
{
    return tag(__FUNCTION__, $content, $options);
}

function h3(mixed $content = '', array $options = [])
{
    return tag(__FUNCTION__, $content, $options);
}

function h4(mixed $content = '', array $options = [])
{
    return tag(__FUNCTION__, $content, $options);
}

function h5(mixed $content = '', array $options = [])
{
    return tag(__FUNCTION__, $content, $options);
}

function h6(mixed $content = '', array $options = [])
{
    return tag(__FUNCTION__, $content, $options);
}

function img(mixed $src = '', string $alt = '', array $options = [])
{
    $options['src'] = $src;
    $options['alt'] = $alt;
    return tag(__FUNCTION__, '', $options);
}
