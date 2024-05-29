<?php

return fn() => [
    'type' => require __DIR__ . '/cms/type.php',
    'category' => require __DIR__ . '/cms/category.php',
    'content' => require __DIR__ . '/cms/content.php',
];
