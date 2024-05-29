<?php

return [
    "components" => [
        "urlManager" => [
            "class" => \codemix\localeurls\UrlManager::class,
            "languages" => explode(",",env("APP_LANGUAGES")),
        ],
    ],
];
