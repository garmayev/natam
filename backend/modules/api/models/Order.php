<?php

namespace backend\modules\api\models;

/**
 * @property array $parameter
 * @property array $tabl
 */
class Order extends \common\models\Order
{
	public function fields()
	{
		return [
			'parameter',
			'Tabl'
		];
	}

	public function getParameter()
	{
		return [
			'numberDate' => \Yii::$app->formatter->asDate($this->created_at, "php:d-m-Y"),
			'numberOrder' => $this->id,
			'INN' => isset($this->client->company_id) ? $this->client->organization->bik : null,
			'customer' => $this->client->name,
			'email' => $this->client->mail,
			'phone' => "+{$this->client->phone}",
			'contact' => $this->comment
 		];
	}

	public function getTabl()
	{
		$result = [];
		foreach ($this->orderProducts as $item) {
			$result["productRow"][] = [
				"kod" => $item->product_id,
				"article" => null,
				"name" => $item->product->title,
				"characteristic" => null,
				"unit" => "шт",
				"quantity" => $item->product_count,
				"cost" => $item->product->price,
				"sum" => $item->product_count * $item->product->price,
				"rateNDS" => null,
				"sumNDS" => null,
			];
		}
		return $result;
	}
}