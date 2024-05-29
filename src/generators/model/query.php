<?php

use yii\helpers\Inflector;

/**
 * This is the template for generating the ActiveQuery class.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */
/* @var $className string class name */
/* @var $modelClassName string related model class name */

$modelFullClassName = $modelClassName;
if ($generator->ns !== $generator->queryNs) {
    $modelFullClassName = '\\' . $generator->ns . '\\' . $modelFullClassName;
}

$extends = ltrim($generator->queryBaseClass, '\\');

$dbName = "db";
foreach($_SERVER['argv'] as $option){
    if(str_starts_with($option, '--db=')){
        $dbName = str_replace('--db=','', $option);
    }
}

$columns = Yii::$app->{$dbName}->getTableSchema($tableName)->columns;
$enumsFunctions = [];
foreach ($columns as $column) {
    $type = $column->dbType;
    $name = $column->name;
    $enumClassName = '\\app\\enums\\' . $modelClassName . Inflector::id2camel($name, '_');
    if (substr($type, 0, 4) == 'enum') {
        preg_match_all("/^enum\(\'(.*)\'\)$/", $type, $matches);
        $enumList = explode("','", $matches[1][0]);
        $enumsFunctions[] = <<< EOF
            /**
            * filter {$name} by custom value
            * @param {$enumClassName} \$value filter by {$name} or list of {$name}
            * @return {$className}
            */
            public function {$name}({$enumClassName} \$value){
                \$this->andWhere(['{$name}' => \$value?->value ?? \$value->name]);
                return \$this;
            }
        EOF;
    }
}
$enumsFunctions = implode("", $enumsFunctions);

$queryNs = $generator->queryNs;

echo <<< EOF
<?php

namespace {$queryNs};

/**
* This is the ActiveQuery class for [[{$modelFullClassName}]].
*
* @see {$modelFullClassName}
*/

class {$className} extends \\{$extends}
{
    /**
    * {@inheritdoc}
    * @return {$modelFullClassName}[]|array
    */
    public function all(\$db = null)
    {
        return parent::all(\$db);
    }

    /**
    * {@inheritdoc}
    * @return {$modelFullClassName}|array|null
    */
    public function one(\$db = null)
    {
        return parent::one(\$db);
    }

    {$enumsFunctions}
}
EOF;
