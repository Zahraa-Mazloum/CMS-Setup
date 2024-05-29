<?php

namespace app\enums;

enum UserType: string
{
    case DEVELOPER = 'developer';
    case MASTER = 'master';
    case ADMIN = 'admin';
    case CUSTOMER = 'customer';

    use \app\components\BaseEnum;
}
