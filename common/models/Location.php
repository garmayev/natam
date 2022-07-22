<?php

namespace common\models;

/**
 *
 * @property int $id [int(11)]
 * @property string $title [varchar(255)]
 * @property float $latitude [double]
 * @property float $longitude [double]
 *
 * @property Order[] $orders
 */
class Location extends \yii\db\ActiveRecord
{
	public function rules()
	{
		return [
			[["latitude", "longitude"], "required"],
			[["latitude", "longitude"], "double"],
			[["title"], "string"]
		];
	}

	public function getOrders()
	{
		return $this->hasMany(Order::class, ["location_id" => "id"]);
	}

	public static function findOrCreate($data)
	{
		$model = self::findOne(["title" => $data["title"]]);
		if (!isset($model)) {
			$model = new Location();
			$model->load(["Location" => $data]) && $model->save();
		}
		return $model;
	}
}