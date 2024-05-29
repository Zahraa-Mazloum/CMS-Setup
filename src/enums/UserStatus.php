<?php

namespace app\enums;

enum UserStatus: string
{
    case ACTIVE = 'enabled';
    case PENDING = 'pending';
    case INACTIVE = 'disabled';

    use \app\components\BaseEnum;
}
