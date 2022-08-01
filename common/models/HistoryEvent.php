<?php

namespace common\models;

/**
 *
 * @property int $id [bigint(20)]
 * @property string $date [datetime]
 * @property string $table [varchar(255)]
 * @property string $field_name [varchar(255)]
 * @property string $field_id [varchar(255)]
 * @property string $old_value
 * @property string $new_value
 * @property int $type [smallint(6)]
 * @property string $user_id [varchar(255)]
 *
 * @property User $user
 */
class HistoryEvent extends \yii\db\ActiveRecord
{
	public static function tableName()
	{
		return "{{%modelhistory}}";
	}

	public function getUser()
	{
		return $this->hasOne(User::class, ["id" => "user_id"]);
	}
}