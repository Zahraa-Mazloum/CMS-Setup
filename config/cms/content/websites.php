<?php 

use app\lib\cms\ContentType;

$setting = new ContentType();

$setting->col1()->field('date')->label('Created Date')->type('date');

return $setting();
