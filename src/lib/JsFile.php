<?php

namespace app\lib;

use yii\base\BaseObject;
use yii\helpers\Json;

class JsFile extends BaseObject
{

    private static $_manifest = null;

    protected static function loadManifest(string $mode, string $tier)
    {
        if (!static::$_manifest) {
            $metaFile = alias("@root/public/$mode/$tier/__meta.json");
            $manifest = [];
            if (is_file($metaFile) && is_readable($metaFile)) {
                $manifest = Json::decode(file_get_contents($metaFile));
            }
            $map = [];
            foreach ($manifest as $k => $data) {
                $map[strtolower($k)] = $data;
            }
            static::$_manifest = $map;
        }
        return static::$_manifest;
    }

    public static function resolveName(string $mode, string $tier, string $filename, $isMainFile = false)
    {
        $key = str_replace('.js', '.ts', "resources/js/tiers/$tier" . ($isMainFile ? '' : "/startup") . "/$filename");
        $map = static::loadManifest($mode, $tier);
        if (array_key_exists($key, $map) && is_array($map[$key])) {
            return $map[$key]['file'];
        }
        return null;
    }

}
