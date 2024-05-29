<?php

namespace app\components;

use Exception;
use yii\base\Component;

class UploadManager extends Component
{

    public function init()
    {
        parent::init();

        ini_set('memory_limit', '-1');
        set_time_limit(0);
    }

    private function getSvgDimensions(string $imageUrl): array
    {
        $parsed_xml     = simplexml_load_string(file_get_contents($imageUrl));
        $svg_attributes = $parsed_xml->attributes();
        $width          = (string) $svg_attributes->width;
        $height         = (string) $svg_attributes->height;

        return [
            $width,
            $height,
            'mime' => 'image/svg+xml',
            'bits' => 0,
        ];
    }

    public function image(string $section, string $base64)
    {
        $aliasPath = '@public/uploads/' . $section . '/' . date('Y/m/d');
        $path = \ensureDirExist($aliasPath);
        $extension = strtolower(explode('+', explode('/', mime_content_type($base64))[1])[0]);
        $filename = rand(1000, 9999) . '-' . rand(1000, 9999) . '-' . rand(1000, 9999) . '-' . rand(1000, 9999) . '-' . time() . '.' . $extension;
        if (file_put_contents($path . '/' . $filename, file_get_contents($base64))) {
            $size = getimagesize($path . '/' . $filename);
            if ($size === false && $extension == "svg") {
                $size = $this->getSvgDimensions($path . '/' . $filename);
            }
            if ($size !== false) {
                return [
                    'url' => \baseAssetUrl() . '/uploads/' . $section . '/' . date('Y/m/d') . '/' . $filename,
                    'path' => $aliasPath . '/' . $filename,
                    'size' => sizeFormat(\filesize($path . '/' . $filename)),
                    'width' => is_array($size) ? $size[0] : 0,
                    'height' => is_array($size) ? $size[1] : 0,
                    'mime' => is_array($size) ? $size['mime'] : null,
                    'bits' => is_array($size) ? $size['bits'] : null,
                    'extension' => $extension,
                ];
            }
        }
        throw new Exception("Image not uploaded, try again");
    }

    public function binaryImage(string $section, array $binary)
    {
        $aliasPath = '@public/uploads/' . $section . '/' . date('Y/m/d');
        $path = \ensureDirExist($aliasPath);
        $targetFile = $path . basename($binary["name"]);
        $extension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $filename = rand(1000, 9999) . '-' . rand(1000, 9999) . '-' . rand(1000, 9999) . '-' . rand(1000, 9999) . '-' . time() . '.' . $extension;

        $size = getimagesize($binary["tmp_name"]);
        if ($size === false && $extension == "svg") {
            $size = $this->getSvgDimensions($path . '/' . $filename);
        }
        if ($size !== false) {
            if (move_uploaded_file($binary["tmp_name"], $path . '/' . $filename)) {
                return [
                    'success' => 1,
                    'file' => [
                        'url' => \baseAssetUrl() . '/uploads/editor/' . date('Y/m/d') . '/' . $filename,
                        'path' => $aliasPath . '/' . $filename,
                        'size' => sizeFormat(\filesize($path . '/' . $filename)),
                        'width' => is_array($size) ? $size[0] : 0,
                        'height' => is_array($size) ? $size[1] : 0,
                        'mime' => is_array($size) ? $size['mime'] : null,
                        'bits' => is_array($size) ? $size['bits'] : null,
                        'extension' => $extension,
                    ],
                ];
            }
        }
        throw new Exception("Image not uploaded, try again");
    }

    public function file(string $section, string $name, string $base64)
    {
        $originalName = pathinfo($name, \PATHINFO_FILENAME);
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $aliasPath = '@public/uploads/' . $section . '/' . date('Y/m/d');
        $path = \ensureDirExist($aliasPath);
        $extension = strtolower(explode('/', mime_content_type($base64))[1]);
        $filename = rand(1000, 9999) . '-' . rand(1000, 9999) . '-' . rand(1000, 9999) . '-' . rand(1000, 9999) . '-' . time() . '.' . $extension;
        if (file_put_contents($path . '/' . $filename, file_get_contents($base64))) {
            return [
                'name' => $originalName,
                'url' => \baseAssetUrl() . '/uploads/' . $section . '/' . date('Y/m/d') . '/' . $filename,
                'path' => $aliasPath . '/' . $filename,
                'size' => sizeFormat(\filesize($path . '/' . $filename)),
                'mime' => mime_content_type($path . '/' . $filename),
                'extension' => $extension,
            ];
        }
        throw new Exception("File not uploaded, try again");
    }
}
