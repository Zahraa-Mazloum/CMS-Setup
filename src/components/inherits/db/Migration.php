<?php

namespace app\components\inherits\db;

use Yii;

/**
 * @property boolean $isSqlServer
 */
class Migration extends \yii\db\Migration
{
    public const EVENT_ADD_RELATION = 'after-add-relation';

    public function getIsSqlServer()
    {
        return strtolower(db()->getDriverName()) == 'sqlsrv';
    }

    /**
     * {@inheritdoc}
     */
    public function insert($table, $columns)
    {
        $this->resolveTableName($table);
        parent::insert($table, $columns);
    }

    /**
     * Resolve the table name and adding it between {{% and }}.
     *
     * @param string $table
     */
    protected function resolveTableName(&$table)
    {
        $re = '/(\{\{\%)?([a-z_]*)(\}\})?/';
        preg_match_all($re, $table, $matches, PREG_SET_ORDER, 0);
        if (isset($matches[0][2])) {
            $table = $matches[0][2];
        }
        $table = '{{%' . $table . '}}';
    }

    /**
     * {@inheritdoc}
     */
    public function batchInsert($table, $columns, $rows)
    {
        $this->resolveTableName($table);
        parent::batchInsert($table, $columns, $rows);
    }

    /**
     * {@inheritdoc}
     */
    public function update($table, $columns, $condition = '', $params = [])
    {
        $this->resolveTableName($table);
        parent::update($table, $columns, $condition, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($table, $condition = '', $params = [])
    {
        $this->resolveTableName($table);
        parent::delete($table, $condition, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function renameTable($table, $newName)
    {
        $this->resolveTableName($table);
        parent::renameTable($table, $newName);
    }

    /**
     * {@inheritdoc}
     */
    public function dropTable($table)
    {
        $this->resolveTableName($table);
        parent::dropTable($table);
    }

    /**
     * {@inheritdoc}
     */
    public function truncateTable($table)
    {
        $this->resolveTableName($table);
        parent::truncateTable($table);
    }

    /**
     * {@inheritdoc}
     */
    public function dropColumn($table, $column)
    {
        $this->resolveTableName($table);
        parent::dropColumn($table, $column);
    }

    /**
     * {@inheritdoc}
     */
    public function renameColumn($table, $name, $newName)
    {
        $this->resolveTableName($table);
        parent::renameColumn($table, $name, $newName);
    }

    /**
     * {@inheritdoc}
     */
    public function alterColumn($table, $column, $type)
    {
        $this->resolveTableName($table);
        parent::alterColumn($table, $column, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function addPrimaryKey($name, $table, $columns)
    {
        $this->resolveTableName($table);
        parent::addPrimaryKey($name, $table, $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function dropPrimaryKey($name, $table)
    {
        $this->resolveTableName($table);
        parent::dropPrimaryKey($name, $table);
    }

    /**
     * {@inheritdoc}
     */
    public function addCommentOnColumn($table, $column, $comment)
    {
        $this->resolveTableName($table);
        parent::addCommentOnColumn($table, $column, $comment);
    }

    /**
     * {@inheritdoc}
     */
    public function addCommentOnTable($table, $comment)
    {
        $this->resolveTableName($table);
        parent::addCommentOnTable($table, $comment);
    }

    /**
     * {@inheritdoc}
     */
    public function dropCommentFromColumn($table, $column)
    {
        $this->resolveTableName($table);
        parent::dropCommentFromColumn($table, $column);
    }

    /**
     * {@inheritdoc}
     */
    public function dropCommentFromTable($table)
    {
        $this->resolveTableName($table);
        parent::dropCommentFromTable($table);
    }

    /**
     * create table process with predefined columns options.
     *
     * @param       $table
     * @param       $columns
     * @param array $extra   predefined columns options such as `permission` | `multilang`
     * @param null  $options
     */
    public function addTable($table, $columns, $extra = [], $options = null)
    {
        if (!empty($extra) && is_array($extra)) {
            foreach ($extra as $name) {
                if ('permission' == $name) {
                    $columns['privilege'] = $this->longText();
                } elseif ('multilang' == $name) {
                    $columns['original_id'] = $this->integer()->null();
                    $columns['language'] = $this->string(10);
                }
            }
        }
        $this->createTable($table, $columns, $options);
    }

    public function longText($length = null)
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder('longText', $length);
    }

    public function enum($name, $list)
    {
        if ($this->isSqlServer) {
            return $this->getDb()->getSchema()->createColumnSchemaBuilder("VARCHAR(32) NOT NULL CHECK($name IN('" . implode("','", $list) . "'))", null);
        }
        return $this->getDb()->getSchema()->createColumnSchemaBuilder('enum("' . implode('","', $list) . '")', null);
    }

    /**
     * {@inheritdoc}
     */
    public function createTable($table, $columns, $options = null)
    {
        $this->resolveTableName($table);
        if (null == $options && 'mysql' === $this->db->driverName) {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $options = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $columns['created_at'] = $this->integer()->notNull();
        $columns['updated_at'] = $this->integer()->notNull();
        parent::createTable($table, $columns, $options);
    }

    public function addRelations($callback)
    {
        try {
            $this->safeOperation($callback);
        } catch (\Throwable $t) {
            $this->printException($t);
        } catch (\Exception $e) {
            $this->printException($e);
        }
    }

    public function safeOperation($callback)
    {
        if (is_callable($callback)) {
            $this->disableKeyCheck();
            $callback($this);
            $this->enableKeyCheck();
        } else {
            throw new \Exception('callback must be callable function');
        }
    }

    public function disableKeyCheck()
    {
        if ($this->isSqlServer) {
            return;
        }
        $this->execute('SET foreign_key_checks = 0');
    }

    public function enableKeyCheck()
    {
        if ($this->isSqlServer) {
            return;
        }
        $this->execute('SET foreign_key_checks = 1');
    }

    /**
     * Add permission field for the table.
     *
     * @param $table
     */
    public function addPermission($table)
    {
        $this->addColumn($table, 'privilege', $this->longText());
    }

    /**
     * {@inheritdoc}
     */
    public function addColumn($table, $column, $type)
    {
        $this->resolveTableName($table);
        parent::addColumn($table, $column, $type);
    }

    public function addMultiLang($table)
    {
        $this->addColumn($table, 'original_id', $this->integer()->null());
        $this->addColumn($table, 'language', $this->string(10));
    }

    public function tinyText($length = null)
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder('tinytext', $length);
    }

    public function mediumText($length = null)
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder('mediumtext', $length);
    }

    public function tinyBlob($length = null)
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder('tinyblob', $length);
    }

    public function blob($length = null)
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder('blob', $length);
    }

    public function mediumBlob($length = null)
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder('mediumblob', $length);
    }

    public function longBlob($length = null)
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder('longblob', $length);
    }

    public function mediumInteger($length = null)
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder('mediumint', $length);
    }

    public function tinyInteger($length = null)
    {
        return parent::tinyInteger($length);
    }

    public function json()
    {
        if ($this->isSqlServer) {
            return 'NVARCHAR(MAX)';
        }
        return parent::json();
    }

    public function manualPrimaryKey()
    {
        if ($this->isSqlServer) {
            return 'INT NOT NULL primary key';
        }
        return 'INT(11) NOT NULL primary key';
    }

    protected function createIndexForeignKey(string $source, string $destination, string $onDelete = null, string $onUpdate = null)
    {
        if (!$onDelete) {
            $onDelete = 'CASCADE';
        }
        if (!$onUpdate) {
            $onUpdate = 'CASCADE';
        }
        [$sourceTable, $sourceAttribute] = explode('.', $source);
        [$destinationTable, $destinationAttribute] = explode('.', $destination);
        $prefix = Yii::$app->getDb()->tablePrefix;
        $index = md5($prefix . '-idx-' . $sourceTable . '-' . $sourceAttribute);
        $foreign = md5($prefix . '-fk-' . $sourceTable . '-' . $sourceAttribute);
        $this->createIndex($index, $sourceTable, $sourceAttribute);
        $this->addForeignKey($foreign, $sourceTable, $sourceAttribute, $destinationTable, $destinationAttribute, $onDelete, $onUpdate);
    }

    /**
     * {@inheritdoc}
     */
    public function createIndex($name, $table, $columns, $unique = false)
    {
        $this->resolveTableName($table);
        parent::createIndex($name, $table, $columns, $unique);
    }

    /**
     * {@inheritdoc}
     */
    public function addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete = null, $update = null)
    {
        $this->resolveTableName($table);
        $this->resolveTableName($refTable);
        parent::addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
    }

    protected function removeIndexForeignKey(string $target)
    {
        [$table, $attribute] = explode('.', $target);
        $prefix = Yii::$app->getDb()->tablePrefix;
        $this->dropForeignKey(md5($prefix . '-fk-' . $table . '-' . $attribute), $prefix . $table);
        $this->dropIndex(md5($prefix . '-idx-' . $table . '-' . $attribute), $prefix . $table);
    }

    /**
     * {@inheritdoc}
     */
    public function dropIndex($name, $table)
    {
        $this->resolveTableName($table);
        parent::dropIndex($name, $table);
    }

    /**
     * {@inheritdoc}
     */
    public function dropForeignKey($name, $table)
    {
        $this->resolveTableName($table);
        parent::dropForeignKey($name, $table);
    }

    /**
     * @param \Throwable|\Exception $e
     */
    private function printException($e)
    {
        echo 'Migration Exception: ' . $e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ")\n";
        echo $e->getTraceAsString() . "\n";
    }

    public function foreignKey(string $source, string $destination, $type = 'integer', string $onDelete = null, string $onUpdate = null)
    {
        $this->on(static::EVENT_ADD_RELATION, function ($event) use ($source, $destination, $onDelete, $onUpdate) {
            $this->safeOperation(
                function () use ($source, $destination, $onDelete, $onUpdate) {
                    $this->createIndexForeignKey($source, $destination, $onDelete, $onUpdate);
                }
            );
        });

        return $this->{$type}();
    }

    public function createRelations()
    {
        $this->trigger(static::EVENT_ADD_RELATION);
    }
}
