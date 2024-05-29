<?php

namespace app\queries;

/**
 * This is the ActiveQuery class for [[\app\models\Session]].
 *
 * @see \app\models\Session
 */

class SessionQuery extends \app\components\inherits\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \app\models\Session[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Session|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
