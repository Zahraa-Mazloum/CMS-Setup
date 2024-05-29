<?php

namespace app\components\inherits\db;

use yii\db\ColumnSchemaBuilder as DbColumnSchemaBuilder;

/**
 * @property boolean $isSqlServer
 */
class ColumnSchemaBuilder extends DbColumnSchemaBuilder
{

    public function getIsSqlServer()
    {
        return strtolower(db()->getDriverName()) == 'sqlsrv';
    }

    /**
     * Builds the unique constraint for the column.
     * @return string returns string 'UNIQUE' if [[isUnique]] is true, otherwise it returns an empty string.
     */
    protected function buildUniqueString()
    {
        if ($this->isSqlServer) {
            return $this->isUnique ? ' UNIQUE NONCLUSTERED' : '';
        }
        return $this->isUnique ? ' UNIQUE' : '';
    }
}
