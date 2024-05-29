<?php

use app\lib\cms\ContentType;

$setting = new ContentType();

$setting->max(1);
$setting->col1()->field("tags")->label("Tags")->type("tags")->defaultValue([]);

return $setting();
