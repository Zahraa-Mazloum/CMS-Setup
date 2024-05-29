<?php

namespace app\queries;

/**
 * This is the ActiveQuery class for [[\app\models\Admin]].
 *
 * @see \app\models\Admin
 */

class AdminQuery extends \app\components\inherits\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \app\models\Admin[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Admin|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
