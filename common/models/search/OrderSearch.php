<?php

namespace common\models\search;

use common\models\Client;
use common\models\Order;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 *
 */
class OrderSearch extends Model
{
	public $id;
	public $client_name;
	public $client_phone;
	public $location_title;
	public $status;
	public $comment;

	public $created_start;
	public $created_finish;
	public $delivery_start;
	public $delivery_finish;

	const TIME_REMAIN = "+1 month";

	public function rules()
	{
		return [
//			[["status"], "in", "range" => [1, 2, 3, 4, 5, 6]],
			[['id'], 'integer'],
//			[["created_start", "created_finish", "delivery_start", "delivery_finish"], "integer"],
			[["client_name", "client_phone", "status", "location_title", "comment"], "safe"],
			[["created_start", "created_finish", "delivery_start", "delivery_finish"], "safe"]
		];
	}

	public function load($data, $formName = null)
	{
		$load = parent::load($data, $formName);
		$scope = isset($formName) ? $formName : $this->formName();
		if ( isset($data[$scope]) ) {
			if ( isset($data[$scope]['created_start']) ) $this->created_start = \Yii::$app->formatter->asTimestamp($data[$scope]['created_start']);
			if ( isset($data[$scope]['created_finish']) ) $this->created_finish = \Yii::$app->formatter->asTimestamp($data[$scope]['created_finish']);
			if ( isset($data[$scope]['delivery_start']) ) $this->delivery_start = \Yii::$app->formatter->asTimestamp($data[$scope]['delivery_start']);
			if ( isset($data[$scope]['delivery_finish']) ) $this->delivery_finish = \Yii::$app->formatter->asTimestamp($data[$scope]['delivery_finish']);
		}
		return $load;
	}

	public function search($params)
	{
		$query = Order::find()->innerJoinWith('client');

		$dataProvider = new ActiveDataProvider([
			"query" => $query,
			"sort" => [
				"defaultOrder" => [
					"id" => SORT_DESC
				]
			],
			'pagination' => [
				'pageSize' => 25,
			],
		]);

		$dataProvider->setSort([
			"attributes" => [
				"id",
				"client.name",
				"client.phone",
				"location.title",
				"price",
				"status",
				"created_at",
				"delivery_date"
			],
			'defaultOrder' => ['id' => SORT_DESC],
		]);

		if ( !\Yii::$app->user->can('employee') ) {
			$client = Client::findOne(['user_id' => \Yii::$app->user->id]);
			if ( $client->organization->boss_id === $client->id ) {
//				$coworkers = Client::findAll(['company_id' => $client->company_id]);
				$query->andFilterWhere(['in', 'client_id', ArrayHelper::map(Client::findAll(['company_id' => $client->company_id]), 'id', 'id')]);
			} else {
				$query->andFilterWhere(['client_id' => $client->id]);
			}
		}

		if (!($this->load($params) && $this->validate())) {
//			$query->andFilterWhere([">", "created_at", strtotime(self::TIME_REMAIN)]);
			return $dataProvider;
		}

		$query
			->andFilterWhere(['order.id' => $this->id])
			->andFilterWhere(["like", "client.name", $this->client_name])
			->andFilterWhere(["like", "order.comment", $this->comment]);

        if (!empty($this->location_title)) {
            $query->innerJoinWith('location');
            $query->andFilterWhere(["like", "location.title", $this->location_title]);
        }

		if ($this->client_phone) {
			$query->andFilterWhere(["like", "client.phone", $this->phoneNormalize()]);
		}

		if (!empty($this->status)) {
			$query->andFilterWhere(["in", "order.status", $this->status]);
		} else {
			$query->andFilterWhere(["in", 'order.status', [1, 2, 3, 4, 5, 6]]);
		}

		if (!empty($this->created_start)) {
			$query->andFilterWhere([">", "order.created_at", $this->created_start]);
		} else {
			$query->andFilterWhere([">", "order.created_at", strtotime(self::TIME_REMAIN)]);
		}
		if (!empty($this->created_finish)) {
			$query->andFilterWhere(["<", "order.created_at", $this->created_finish]);
		} else {
			$query->andFilterWhere(["<", "order.created_at", time()]);
		}

		if (!empty($this->delivery_start)) {
			$query->andFilterWhere([">", "order.delivery_date", \Yii::$app->formatter->asTimestamp($this->delivery_start)]);
		}
		if (!empty($this->delivery_finish)) {
			$query->andFilterWhere(["<", "order.delivery_date", \Yii::$app->formatter->asTimestamp($this->delivery_finish)]);
		}

		\Yii::error($query->createCommand()->getRawSql());

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