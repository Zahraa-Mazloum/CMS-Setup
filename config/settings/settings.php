<?php

use app\lib\cms\Setting;

$setting = new Setting();

$general = $setting->category("general")->label("General");
$general->field("logo")->type("image")->label("Logo")->defaultValue("")->attribute("accept", "images/*");
$general->field("year")->type("string")->label("Year")->defaultValue("1987")->attribute("type", "number")->attribute("min", 0)->attribute("max", 9999);

$ip = $setting->repeater("allowedIp");
$general->field("allowedIp")->label("Allow Access From IP")->type("repeater")->defaultValue([])->attribute('style','width:80%')->prop("layout", [
    [
        $ip->field("ip")->label("IP")->type("string")->prop('noLabel', true)->toArray(),
    ]
]);

$social = $setting->category("social")->label("Social Media");
$social->field("facebook")->label("Facebook")->icon("mdiFacebook")->defaultValue("#");
$social->field("twitter")->label("Twitter")->icon("mdiTwitter")->defaultValue("#");
$social->field("youtube")->label("Youtube")->icon("mdiYoutube")->defaultValue("#");

return $setting();
