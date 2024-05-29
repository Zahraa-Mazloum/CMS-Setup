<?php

namespace app\enums;

enum SettingType: string
{
    case SYSTEM = 'system';
    case REFERENCE = 'reference';
    case USER = 'user';

    use \app\components\BaseEnum;
}
