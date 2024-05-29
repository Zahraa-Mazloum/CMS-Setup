<?php

return function () {
    return [
        '@root' => dirname(__DIR__),
        '@src' => '@root/src',
        '@app' => '@src',
        '@config' => '@root/config',
        '@public' => '@root/public',
        '@resources' => '@root/resources',
        '@runtime' => '@root/runtime',
        '@vendor' => '@root/vendor',
        '@node_modules' => '@root/node_modules',
    ];
};
