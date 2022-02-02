<?php

namespace common\models\search;

use common\models\Client;
use common\models\Order;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class OrderSearch extends Model
{
	public $client_name;
	public $client_phone;
	public $location_title;
	public $status;
	public $comment;

	public $created_start;
	public $created_finish;
	public $delivery_start;
	public $delivery_finish;

	public function rules()
	{
		return [
			[["client_name", "client_phone", "location_title", "status", "comment"], "safe"],
			[["created_start", "created_finish", "delivery_start", "delivery_finish"], "safe"]
		];
	}

	public function search($params)
	{
		$query = Order::find();

//		$query = self::find();
		$dataProvider = new ActiveDataProvider([
			"query" => $query,
			"sort" => [
				"defaultOrder" => [
					"delivery_date" => SORT_DESC,
					"created_at" => SORT_DESC,
				]
			]
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$query->innerJoinWith("client", true);
		$query->innerJoinWith("location", true);


		$query->filterWhere(["<", "status", Order::STATUS_COMPLETE])
			->andFilterWhere(["like", "client.name", $this->client_name])
			->andFilterWhere(["like", "location.title", $this->location_title])
			->andFilterWhere(["like", "comment", $this->comment])
//			->andFilterWhere([">=", "created_at", \Yii::$app->formatter->asTimestamp($this->created_start)])
//			->andFilterWhere(["<=", "created_at", \Yii::$app->formatter->asTimestamp($this->created_finish)])
//			->andFilterWhere([">=", "delivery_date", \Yii::$app->formatter->asTimestamp($this->created_start)])
//			->andFilterWhere(["<=", "delivery_date", \Yii::$app->formatter->asTimestamp($this->created_finish)])
		;

//		$query->andFilterWhere(["<=", "created_at", \Yii::$app->formatter->asTimestamp(isset($this->created_start) ? \Yii::$app->formatter->asTimestamp($this->created_start) : 0)]);
//		$query->andFilterWhere([">=", "created_at", \Yii::$app->formatter->asTimestamp(isset($this->created_finish) ? \Yii::$app->formatter->asTimestamp($this->created_finish) : \Yii::$app->formatter->asTimestamp(date()))]);
		if ($this->client_phone) {
			$query->andFilterWhere(["like", "phone", $this->phoneNormalize()]);
		}

		if ($this->created_start) {
			$query->andFilterWhere([">=", "created_at", \Yii::$app->formatter->asTimestamp($this->created_start)]);
		}
		if ($this->created_finish) {
			$query->andFilterWhere(["<=", "created_at", \Yii::$app->formatter->asTimestamp($this->created_finish)]);
		}

		if ($this->delivery_start) {
			$query->andFilterWhere([">=", "delivery_date", \Yii::$app->formatter->asTimestamp($this->delivery_start)]);
		}
		if ($this->delivery_finish) {
			$query->andFilterWhere(["<=", "delivery_date", \Yii::$app->formatter->asTimestamp($this->delivery_finish)]);
		}
//		var_dump("Created Start ".\Yii::$app->formatter->asTimestamp($this->created_start)."<br />");
//		var_dump("Delivery Start ".\Yii::$app->formatter->asTimestamp($this->delivery_start)."<br />");
//		var_dump("Delivery Finish ".\Yii::$app->formatter->asTimestamp($this->delivery_finish)."<br />");
//		$query->andFilterWhere(["<=", "delivery_date", \Yii::$app->formatter->asTimestamp(isset($this->delivery_start) ? \Yii::$app->formatter->asTimestamp($this->delivery_start) : 0)]);
//		$query->andFilterWhere([">=", "delivery_date", \Yii::$app->formatter->asTimestamp(isset($this->delivery_finish) ? \Yii::$app->formatter->asTimestamp($this->delivery_finish) : time())]);
//		var_dump($query->createCommand()->getRawSql()); die;

		return $dataProvider;
	}

	private function phoneNormalize()
	{
		$phone = trim($this->client_phone);

		$res = preg_replace(
			array(
				'/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{3})[-|\s]?\)[-|\s]?(\d{3})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
				'/[\+]?([7|8])[-|\s]?(\d{3})[-|\s]?(\d{3})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
				'/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{4})[-|\s]?\)[-|\s]?(\d{2})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
				'/[\+]?([7|8])[-|\s]?(\d{4})[-|\s]?(\d{2})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
				'/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{4})[-|\s]?\)[-|\s]?(\d{3})[-|\s]?(\d{3})/',
				'/[\+]?([7|8])[-|\s]?(\d{4})[-|\s]?(\d{3})[-|\s]?(\d{3})/',
			),
			array(
				'+7 $2 $3-$4-$5',
				'+7 $2 $3-$4-$5',
				'+7 $2 $3-$4-$5',
				'+7 $2 $3-$4-$5',
				'+7 $2 $3-$4',
				'+7 $2 $3-$4',
			),
			$phone
		);

		return $res;
	}
}