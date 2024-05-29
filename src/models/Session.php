<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%session}}".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $token
 * @property string|null $os
 * @property string|null $browser
 * @property string|null $device
 * @property string|null $ip
 * @property string|null $start_time
 * @property int|null $active
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 */
class Session extends \app\components\inherits\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%session}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'active'], 'integer'],
            [['start_time'], 'safe'],
            [['token'], 'string', 'max' => 500],
            [['os', 'browser', 'device', 'ip'], 'string', 'max' => 255],
            [['token'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'token' => 'Token',
            'os' => 'Os',
            'browser' => 'Browser',
            'device' => 'Device',
            'ip' => 'Ip',
            'start_time' => 'Start Time',
            'active' => 'Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\queries\SessionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\queries\SessionQuery(get_called_class());
    }

    public static function add($userId, $authKey): static
    {
        $model = new static();
        $model->user_id = $userId;
        $model->token = $authKey;
        $model->os = platformName();
        $model->browser = browserName();
        $model->device = deviceFamily();
        $model->ip = userIp();
        $model->start_time = date('Y-m-d H:i:s');
        $model->active = 1;
        $model->save();
        return $model;
    }

    public static function logout($identity, $token)
    {
        if ($identity) {
            return static::deleteAll(['user_id' => $identity->id, 'token' => $token]);
        }
        return null;
    }
}
