<?php

namespace common\models;

/**
 *
 * @property int $id [int(11)]
 * @property string $title [varchar(255)]
 * @property float $latitude [double]
 * @property float $longitude [double]
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
}