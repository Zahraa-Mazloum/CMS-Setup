<?php

namespace app\queries;

/**
 * This is the ActiveQuery class for [[\app\models\Content]].
 *
 * @see \app\models\Content
 */

class ContentQuery extends \app\components\inherits\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \app\models\Content[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Content|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * filter status by custom value
     * @param \app\enums\ContentStatus $value filter by status or list of status
     * @return ContentQuery
     */
    public function status(\app\enums\ContentStatus $value)
    {
        $this->andWhere(['status' => $value?->value ?? $value->name]);
        return $this;
    }
}
