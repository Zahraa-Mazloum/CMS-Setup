<?php

namespace app\queries;

/**
 * This is the ActiveQuery class for [[\app\models\Category]].
 *
 * @see \app\models\Category
 */

class CategoryQuery extends \app\components\inherits\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \app\models\Category[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Category|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * filter status by custom value
     * @param \app\enums\CategoryStatus $value filter by status or list of status
     * @return CategoryQuery
     */
    public function status(\app\enums\CategoryStatus $value)
    {
        $this->andWhere(['status' => $value?->value ?? $value->name]);
        return $this;
    }
}
