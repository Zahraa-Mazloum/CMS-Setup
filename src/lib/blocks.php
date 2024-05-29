<?php

function blocksToText($data)
{
    $blocks = $data['blocks'] ?? [];
    $text = [];
    foreach ($blocks as $block) {
        if (in_array($block['type'], ['paragraph', 'header'])) {
            $text[] = $block['data']['text'];
        } else if (in_array($block['type'], ['warning'])) {
            $text[] = $block['data']['title'];
            $text[] = $block['data']['message'];
        } else if (in_array($block['type'], ['list'])) {
            nestedLoop(function ($item) use (&$text) {
                $text[] = $item['content'];
            }, $block['data']['items']);
        } else if (in_array($block['type'], ['checklist'])) {
            nestedLoop(function ($item) use (&$text) {
                $text[] = $item['text'];
            }, $block['data']['items']);
        } else if (in_array($block['type'], ['table'])) {
            nestedLoop(function ($item) use (&$text) {
                foreach ($item as $word) {
                    $text[] = $word;
                }
            }, $block['data']['content']);
        } else if (in_array($block['type'], ['embed'])) {
            $text[] = $block['data']['service'];
            $text[] = $block['data']['caption'];
        } else   if (in_array($block['type'], ['raw'])) {
            $text[] = $block['data']['html'];
        } else   if (in_array($block['type'], ['alert'])) {
            $text[] = $block['data']['message'];
        } else   if (in_array($block['type'], ['linkTool'])) {
            $text[] = $block['data']['meta']['title'];
            $text[] = $block['data']['meta']['description'];
            $text[] = $block['data']['meta']['keywords'];
            $text[] = $block['data']['meta']['site_name'];
        }
    }
    foreach ($text as &$line) {
        $line = strip_tags($line);
    }
    return implode("\n", $text);
}
