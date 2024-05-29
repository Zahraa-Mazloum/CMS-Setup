<?php

namespace app\queries;

/**
 * This is the ActiveQuery class for [[\app\models\CategoryLocal]].
 *
 * @see \app\models\CategoryLocal
 */

class CategoryLocalQuery extends \app\components\inherits\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \app\models\CategoryLocal[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\CategoryLocal|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
