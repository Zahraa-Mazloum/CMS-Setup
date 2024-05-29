<?php

namespace app\queries;

/**
 * This is the ActiveQuery class for [[\app\models\ContentLocal]].
 *
 * @see \app\models\ContentLocal
 */

class ContentLocalQuery extends \app\components\inherits\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \app\models\ContentLocal[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\ContentLocal|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
