<?php

namespace app\queries;

/**
 * This is the ActiveQuery class for [[\app\models\Seo]].
 *
 * @see \app\models\Seo
 */

class SeoQuery extends \app\components\inherits\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \app\models\Seo[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Seo|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
