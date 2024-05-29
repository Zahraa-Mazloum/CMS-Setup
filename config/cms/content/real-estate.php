<?php

use app\lib\cms\ContentType;

$setting = new ContentType();

$setting->linked();
$setting->col1()->field("phone")->label("Phone Number")->type("phone")->defaultValue(null)->prop("defaultCountry", "SA")->prop("defaultCallingCode", "966");
$setting->col1()->field("date")->label("Date")->type("date")->defaultValue(null)->prop("cleanable", true);
$setting->col1()->field("available_until")->label("Available Until")->type("date")->defaultValue(null)->prop("cleanable", true)->prop("popover", true);

$schedule = $setting->repeater("schedule");
$setting->col1()->field("schedule")->label("Schedule")->type("repeater")->defaultValue([])->prop("layout", [
    [
        $schedule->field("open")->type("string")->label("Open")->defaultValue("")->toArray(),
        $schedule->field("close")->type("string")->label("Close")->defaultValue("")->toArray(),
    ],
    [
        $schedule->field("image")->label("Image")->type("image")->prop("accept", ["image/jpg","image/jpeg"])->prop("aspect-ratio", 4 / 3)->prop("crop", true)->toArray()
    ],
    [
        $schedule->field("date")->label("Date")->type("date")->toArray(),
    ]
]);
$setting->col2()->field("image")->label("Image")->type("image")->prop("accept", ["image/jpg","image/jpeg"])->prop("aspect-ratio", 4 / 3)->prop("crop", true);
$setting->col3()->field("gallery")->label("Gallery")->type("gallery")->prop("accept", ["image/jpg","image/jpeg"])->prop("aspect-ratio", 4 / 3)->prop("crop", true);

return $setting();
