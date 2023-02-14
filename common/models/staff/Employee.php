<?php

namespace common\models\staff;

use common\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 *
 * @property int $id [int(11)]
 * @property string $name [varchar(255)]
 * @property string $family [varchar(255)]
 * @property string $phone [varchar(255)]
 * @property int $birthday [int(11)]
 * @property int $user_id [int(11)]
 * @property int $state_id [int(11)]
 * @property int $chat_id [int(11)]
 * @property int $last_message_at [int(11)]
 * @property string $car
 * @property float $engine [double]
 * @property int $level [int(11)]
 *
 * @property string $fullname
 * @property User $user
 * @property State $state
 */
class Employee extends ActiveRecord
{
    public $birth;

    public function attributeLabels()
    {
        return [
            "name" => Yii::t("app", 'Name'),
            "family" => Yii::t("app", "Family"),
            "phone" => Yii::t("app", "Phone"),
            "birthday" => Yii::t("app", "Birthday"),
            "state" => Yii::t("app", "State"),
            "fullname" => Yii::t("app", "Fullname"),
        ];
    }

    public function rules()
    {
        return [
            [['name', 'family', 'car', 'birth', 'phone'], 'string'],
            [['birthday', 'chat_id', 'last_message_at', 'level'], 'integer'],
            [['engine'], 'double'],
            [['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['state_id'], 'exist', 'targetClass' => State::class, 'targetAttribute' => ['state_id' => 'id']],
        ];
    }

	public function beforeSave($insert)
	{
		$valid = parent::beforeSave($insert);
		if ($valid) {
			$user = User::findOne(['username' => $this->phone]);
			if (isset($user)) {
				$this->user_id = $user->id;
			} else {
				$user = Yii::createObject([
					'class' => User::className(),
					'scenario' => 'register',
					'username' => $this->phone,
					'email' => "{$this->phone}@employee.com",
					'password' => $this->phone,
				]);
				if ($user->save()) {
					$auth = Yii::$app->authManager;
					$role = $auth->getRole('employee');
					$auth->assign($role, $user->id);
					$user->profile->name = $this->name;
					$user->profile->public_email = "{$this->phone}@employee.com";
					$this->user_id = $user->id;
					return $valid && $user->profile->save();
				} else {
					Yii::error($user->getErrorSummary(true));
				}
			}
		}
		return $valid;
	}

    public function load($data, $formName = null)
    {
        $scope = isset($formName) ? $formName : $this->formName();
        $this->birthday = Yii::$app->formatter->asTimestamp($data[$scope]["birth"]);
        return parent::load($data, $formName);
    }

    public function getFullname()
    {
        return "{$this->family} {$this->name}";
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ["id" => "user_id"]);
    }


    public function getState()
    {
        return $this->hasOne(State::class, ['id' => 'state_id']);
    }

    public function setBirthday($value)
    {
        if (is_int($value)) {
            $this->birthday = $value;
        } else {
            $this->birthday = Yii::$app->formatter->asTimestamp($value);
        }
    }
}