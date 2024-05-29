<?php

namespace app\components\inherits\db;

use yii\db\ExpressionInterface;

class ActiveQuery extends \yii\db\ActiveQuery
{
    public function sort(string|array|ExpressionInterface|null $columns){
        if(is_string($columns)){
            $columns = implode(", ",array_map(function($value){
                if(str_starts_with($value, "-")){
                    return str_replace("-", "", $value)." DESC";
                }
                return $value;
            }, explode(',', $columns)));
        }
        return $this->orderBy($columns);
    }
}
