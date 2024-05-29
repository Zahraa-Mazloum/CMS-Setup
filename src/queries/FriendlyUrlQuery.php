<?php

namespace app\queries;

/**
 * This is the ActiveQuery class for [[\app\models\FriendlyUrl]].
 *
 * @see \app\models\FriendlyUrl
 */

class FriendlyUrlQuery extends \app\components\inherits\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \app\models\FriendlyUrl[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\FriendlyUrl|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
