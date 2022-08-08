<?php

namespace common\models\search;

use common\models\staff\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 *
 */
class EmployeeSearch extends Model
{
    public $state_name;
    public $fullname;
    public $phone;

    public function rules()
    {
        return [
            [['fullname', 'phone', 'state_name'], 'string'],
        ];
    }

    public function search($params)
    {
        $query = Employee::find()->joinWith('state');

        $dataProvider = new ActiveDataProvider([
            "query" => $query
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if (isset($this->fullname) && !empty($this->fullname)) {
            $query->filterWhere(['or', ['like', 'name', $this->fullname], ['like', 'family', $this->fullname]]);
        }

        $query->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'state.title', $this->state_name]);

        return $dataProvider;
    }
}