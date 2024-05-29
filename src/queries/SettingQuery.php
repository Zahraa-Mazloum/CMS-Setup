<?php

namespace app\queries;

/**
 * This is the ActiveQuery class for [[\app\models\Setting]].
 *
 * @see \app\models\Setting
 */

class SettingQuery extends \app\components\inherits\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \app\models\Setting[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Setting|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * filter type by custom value
     * @param \app\enums\SettingType $value filter by type or list of type
     * @return SettingQuery
     */
    public function type(\app\enums\SettingType $value)
    {
        $this->andWhere(['type' => $value?->value ?? $value->name]);
        return $this;
    }
}
