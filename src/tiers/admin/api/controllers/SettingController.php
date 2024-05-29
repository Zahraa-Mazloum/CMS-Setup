<?php

namespace app\tiers\admin\api\controllers;

use app\components\inherits\rest\SController;
use app\enums\SettingType;
use app\models\Setting;
use yii\db\Expression;

class SettingController extends SController
{
    public function actionReferences()
    {
        $startWith = get('startWith');
        return Setting::find()->where(new Expression('name like :startWith', [":startWith" => "$startWith%"]))->andWhere(['type' => SettingType::REFERENCE->value])->all();
    }

    public function actionSaveReference()
    {
        $oldId = post('oldId');
        $newId = post('newId');
        $value = post('value');
        if ($oldId) {
            Setting::updateAll(['value' => null], ['id' => $oldId]);
        }
        Setting::updateAll(['value' => $value], ['id' => $newId]);
        return true;
    }

    public function actionSystem()
    {
        return Setting::find()->where(['type' => SettingType::SYSTEM->value])->all();
    }

    public function actionSaveSystem()
    {
        $name = post('name');
        $value = post('value');
        return Setting::setSystem($name, $value, false);
    }
}
