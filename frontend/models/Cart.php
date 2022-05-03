<?php

namespace frontend\models;

use common\models\Product;

/**
 *
 * @property int $id [int(11)]
 * @property string $session_id [varchar(255)]
 * @property int $user_id [int(11)]
 * @property string $product_id [varchar(255)]
 * @property string $product_count [varchar(255)]
 */
class Cart extends \yii\db\ActiveRecord
{
	protected $product_model = Product::class;

	public static function tableName()
	{
		return "{{%cart}}";
	}

	public function add($id, $quantity)
	{
		$this->product_id = static ($this->product_model)::findOne($id);
		$this->product_count = $quantity;
		$this->save();
	}
}