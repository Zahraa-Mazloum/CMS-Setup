<?php

namespace app\queries;

/**
 * This is the ActiveQuery class for [[\app\models\ContentCategory]].
 *
 * @see \app\models\ContentCategory
 */

class ContentCategoryQuery extends \app\components\inherits\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \app\models\ContentCategory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\ContentCategory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
