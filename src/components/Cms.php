<?php

namespace app\components;

use app\enums\ContentStatus;
use app\models\Content;
use app\models\ContentLocal;
use app\models\Setting;
use yii\base\Component;

class Cms extends Component
{
    public function syncContents(?int $entityId = null, ?callable $callback = null, string | array $include = '*')
    {
        $linked = $entityId > 0;
        $path = alias('@config/cms/content');
        $files = array_diff(\scandir($path), ['..', '.','.gitkeep']);
        $count = count($files);
        $callback && $callback("Have $count ");
        foreach ($files as $file) {
            $basename = \pathinfo($file, \PATHINFO_FILENAME);
            if ($include == '*' || (is_array($include) && in_array($basename, $include))) {
                $filename = "$path/$file";
                $data = require $filename;
                $max = $data['max'] ?? 0;
                $dataLink = $data['linked'] ?? false;
                if ($linked == $dataLink) {
                    $callback && $callback(" - $file \n");
                    for ($i = 0; $i < $max; $i++) {
                        $keyId = $entityId > 0 ? ".$entityId" : "";
                        $typeId = $entityId > 0 ? "[$entityId]" : "";
                        $key = "cms.contents$keyId.$basename" . ($max > 1 ? "." . ($i + 1) : '');
                        $callback && $callback("   Reference : $key\n");
                        $id = Setting::reference($key)->value;
                        $content = Content::find()->where(['id' => $id])->one();
                        if (!$content) {
                            $content = new Content();
                            $content->type = "$typeId$basename";
                            $content->status = ContentStatus::ACTIVE->value;
                            $content->position = 0;
                            $content->attrs = [];
                            if ($content->save()) {
                                // create content langs
                                foreach (languages() as $language) {
                                    $lang = new ContentLocal();
                                    $lang->content_id = $content->id;
                                    $lang->language = $language['value'];
                                    $lang->title = ucfirst($basename) . ($max > 1 ? " " . ($i + 1) : '');
                                    $lang->excerpt = null;
                                    $lang->blocks = [];
                                    $lang->plain_text = "";
                                    $lang->attrs = [];
                                    $lang->save();
                                }
                            }
                        }
                        // assign reference setting
                        Setting::setReference($key, "$content->id");
                    }
                    $callback && $callback("\n");
                }
            }
        }
        $callback && $callback("DONE!\n");
    }
}
