<?php

namespace app\queries;

/**
 * This is the ActiveQuery class for [[\app\models\User]].
 *
 * @see \app\models\User
 */

class UserQuery extends \app\components\inherits\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \app\models\User[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * filter status by custom value
     * @param \app\enums\UserStatus $value filter by status or list of status
     * @return UserQuery
     */
    public function status(\app\enums\UserStatus $value)
    {
        $this->andWhere(['status' => $value?->value ?? $value->name]);
        return $this;
    }
    /**
     * filter type by custom value
     * @param \app\enums\UserType $value filter by type or list of type
     * @return UserQuery
     */
    public function type(\app\enums\UserType $value)
    {
        $this->andWhere(['type' => $value?->value ?? $value->name]);
        return $this;
    }
}
