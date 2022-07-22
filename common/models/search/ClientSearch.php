<?php

namespace common\models\search;

use common\models\Client;
use yii\data\ActiveDataProvider;

class ClientSearch extends \yii\base\Model
{
	public $name;
	public $phone;
	public $email;
	public $company;

	public function rules()
	{
		return [
			[['name', 'phone', 'email', 'company'], 'string'],
		];
	}

	public function search($params)
	{
		$query = Client::find();

		$dataProvider = new ActiveDataProvider([
			"query" => $query,
			"sort" => [
				"defaultOrder" => [
					"id" => SORT_DESC
				]
			]
		]);

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$query->filterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'email', $this->email])
			->andFilterWhere(['like', 'company', $this->company]);
		if ($this->phone) {
			$query->andFilterWhere(["like", "phone", $this->phoneNormalize()]);
		}

		return $dataProvider;
	}

	private function phoneNormalize()
	{
		$phone = trim($this->phone);

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