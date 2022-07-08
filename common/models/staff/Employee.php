<?php

namespace common\models\staff;

use common\models\User;

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
 *
 * @property string fullname
 * @property User $user
 * @property State $state
 */
class Employee extends \yii\db\ActiveRecord
{
	public $birth;
	public function attributeLabels()
	{
		return [
			"name" => \Yii::t("app", 'Name'),
			"family" => \Yii::t("app", "Family"),
			"phone" => \Yii::t("app", "Phone"),
			"birthday" => \Yii::t("app", "Birthday"),
			"state" => \Yii::t("app", "State"),
			"fullname" => \Yii::t("app", "Fullname"),
		];
	}

	public function rules()
	{
		return [
			[['name', 'family', 'car', 'birth'], 'string'],
			[['birthday', 'chat_id', 'last_message_at'], 'integer'],
			[['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			[['state_id'], 'exist', 'targetClass' => State::class, 'targetAttribute' => ['state_id' => 'id']],
		];
	}

	public function load($data, $formName = null)
	{
		$scope = isset($formName) ? $formName : $this->formName();
		$this->birthday = \Yii::$app->formatter->asTimestamp($data[$scope]["birth"]);
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
		if ( is_int($value) ) {
			$this->birthday = $value;
		} else {
			$this->birthday = \Yii::$app->formatter->asTimestamp($value);
		}
	}
}