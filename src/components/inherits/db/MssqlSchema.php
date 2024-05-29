<?php

namespace app\components\inherits\db;

use yii\db\mssql\Schema;

class MssqlSchema extends Schema {

    public function createColumnSchemaBuilder($type, $length = null)
    {
        return new ColumnSchemaBuilder($type, $length);
    }

}
