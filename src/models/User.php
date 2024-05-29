<?php

namespace app\models;

use app\enums\UserStatus;
use app\enums\UserType;
use Yii;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id
 * @property string|null $username
 * @property string|null $password_hash
 * @property string|null $password_reset_token
 * @property string|null $status
 * @property string|null $type
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Admin $admin
 * @property Session[] $sessions
 * @property UserChannel[] $userChannels
 * @property Channel[] $channels
 */
class User extends \app\components\inherits\db\ActiveRecord implements IdentityInterface
{

    public $password = null;

    public function init()
    {
        parent::init();
        $this->on(User::EVENT_BEFORE_VALIDATE, function ($e) {
            if ($this->password) {
                $this->setPassword($this->password);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'type'], 'string'],
            [['username', 'password_hash', 'password_reset_token', 'password'], 'string', 'max' => 255],
            [['username'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'status' => 'Status',
            'type' => 'Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(Admin::class, ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return match ($this->type) {
            default => $this->getAdmin(),
        };
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSessions()
    {
        return $this->hasMany(Session::class, ['user_id' => 'id']);
    }

    public function getUserChannels()
    {
        return $this->hasMany(ChannelUser::class, ['user_id' => 'id'])->where(['active' => 1]);
    }

    public function getChannels()
    {
        return $this->hasMany(Channel::class, ['id' => 'channel_id'])->via('userChannels');
    }

    /**
     * {@inheritdoc}
     * @return \app\queries\UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\queries\UserQuery(get_called_class());
    }

    public function validatePassword($password)
    {
        return security()->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password)
    {
        $this->password_hash = security()->generatePasswordHash($password);

        return true;
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function validateAuthKey($authKey)
    {
        $auth = false;
        if ($authKey) {
            $auth = $this->getSessions()->where(['token' => $authKey])->one();
        }

        return null !== $auth;
    }

    public function generateAuthKey()
    {
        $model = Session::add($this->id, security()->generateRandomString());
        return $model->token;
    }

    public static function findByUsername($username)
    {
        return static::find()->where(new Expression('username = :username'), [':username' => $username])->andWhere(['status' => UserStatus::ACTIVE->value])->one();
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => UserStatus::ACTIVE->value]);
    }

    public function getAuthKey(){}

    public static function findIdentityByAccessToken($token, $type = null)
    {
        /** @var Token $token */
        $query = static::find();
        $query->from(['user' => static::tableName()]);
        $query->joinWith([
            'sessions' => function ($query) {
                /*
                 * @var $query ActiveQuery
                 */
                $query->from(['sessions' => Session::tableName()]);
            },
        ], true, 'INNER JOIN');
        $query->where([
            'user.status' => UserStatus::ACTIVE->value,
            'user.id' => $token->get('uid'),
            'sessions.token' => $token->get('auth_key'),
        ]);

        return $query->one();
    }

    public function fields()
    {
        $list = parent::fields();
        unset($list['password_hash'], $list['password_reset_token']);
        $list['typeName'] = function () {
            return ucfirst($this->type);
        };
        $list['statusName'] = function () {
            return ucfirst($this->status);
        };
        return $list;
    }

    public static function notificationSubscription($token, $platform, $user = null)
    {
        if (isAuth() && !$user) {
            $user = identity();
        }
        return false;
    }

    public function extraFields()
    {
        return ['admin', 'customer', 'profile', 'sessions', 'userChannels', 'channels'];
    }
}
