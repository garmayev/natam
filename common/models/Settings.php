<?php

namespace common\models;

/**
 *
 * @property int $id [int(11)]
 * @property string $name [varchar(255)]
 * @property string $content
 */
class Settings extends \yii\db\ActiveRecord
{
	public static function tableName()
	{
		return "settings";
	}

	public function getContent()
	{
		return json_decode($this->content, true);
	}

	public function setContent($value)
	{
		$this->content = json_encode($value);
	}

	public static function getInterval($index)
	{
		$value = json_decode(Settings::findOne(1)->content, true);
		return (isset($value["notify"]["limit"][$index])) ? $value["notify"]["limit"][$index] : 0;
	}

	public static function getDeliveryCost()
	{
		$value = json_decode(Settings::findOne(1)->content, true);
		if ( isset($value["delivery_cost"]) ) {
			return $value["delivery_cost"];
		} else {
			return 0;
		}
	}
}