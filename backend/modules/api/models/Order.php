<?php

namespace backend\modules\api\models;

use common\models\Settings;

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
        if ( isset($this->client) )
		return [
			'numberDate' => \Yii::$app->formatter->asDate($this->created_at, "php:d-m-Y H:i"),
			'numberOrder' => $this->id,
			'INN' => isset($this->client->company_id) ? $this->client->organization->inn : null,
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
				"article" => $this->article,
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
        if ((is_null($this->delivery_city) || $this->delivery_city !== 1) && ($this->delivery_type === \common\models\Order::DELIVERY_STORE) && !empty($this->delivery_distance)) {
            $result["productRow"][] = [
                "kod" => 0,
                "article" => null,
                "name" => \Yii::t("app", "Delivery"),
                "characteristic" => null,
                "unit" => "Км",
                "quantity" => $this->delivery_distance,
                "cost" => Settings::getDeliveryCost(),
                "sum" => $this->delivery_distance * Settings::getDeliveryCost(),
                "rateNDS" => null,
                "sumNDS" => null,
            ];
        }
		return $result;
	}
}