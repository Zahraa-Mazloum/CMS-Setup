<?php

namespace app\tiers\admin\api\controllers;

use app\components\inherits\rest\SController;
use app\enums\ContentStatus;
use app\enums\UserStatus;
use app\enums\UserType;
use app\models\Content;
use app\models\User;
use yii\helpers\Inflector;

class StatisticController extends SController
{
    public function actionNumbers()
    {
        $data = [];

        $cmsTypes = param('cms_type');
        foreach($cmsTypes as $type){
            $name = $type['name'];
            if($name == "default"){
                $name .= ".%";
            }
            $data[] = $this->contents($name, Inflector::id2camel($type['label']));
        }

        $data[] = $this->admins();

        $select = [];
        $from = [];
        foreach($data as [$sqlSelect, $sqlFrom]){
            $select[] = $sqlSelect;
            $from[] = $sqlFrom;
        }
        $select = implode(',', $select);
        $from = implode(',', $from);
        $sql = <<< SQL
        SELECT JSON_ARRAY({$select}) as `numbers`
        FROM 
            {$from}
        SQL;
        return db()->createCommand($sql)->queryOne();
    }

    private function admins()
    {
        $user = User::getTableSchema()->name;
        $adminType = UserType::ADMIN->value;
        $status = UserStatus::ACTIVE->value;
        $select = '`admins`.`value`';
        $sql = <<< SQL
        (
            SELECT JSON_OBJECT('name', "Admins", 'count', count(*)) as `value`
            FROM `{$user}` as `u`
            WHERE `u`.`type` = "{$adminType}" AND `u`.`status` = "{$status}"
        ) as `admins` 
        SQL;
        return [$select, $sql];
    }

    private function contents($type, $name){
        $label = Inflector::camel2words($name);
        $content = Content::getTableSchema()->name;
        $status = ContentStatus::ACTIVE->value;
        $select = "`{$name}`.`value`";
        $sql = <<< SQL
        (
            SELECT JSON_OBJECT('name', "{$label}", 'count', count(*)) as `value`
            FROM `{$content}` as `c`
            WHERE `c`.`status` = "{$status}"  AND `c`.`type` like "{$type}"
        ) as `{$name}`
        SQL;
        return [$select, $sql];
    }
}
