<?php

$out = [];

$path = __DIR__ . DIRECTORY_SEPARATOR . 'content';

$files = scandir($path);
foreach ($files as $file) {
    if (!in_array($file, ['.', '..', '.gitkeep'])) {
        $filePath = "$path/$file";
        $name = pathinfo($filePath, PATHINFO_FILENAME);
        $out[$name] = require $filePath;
    }
}

return $out;
