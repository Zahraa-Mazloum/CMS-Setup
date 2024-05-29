<?php

namespace app\enums;

enum CategoryStatus: string
{
    case ACTIVE = 'active';
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case DELETED = 'deleted';

    use \app\components\BaseEnum;
}
