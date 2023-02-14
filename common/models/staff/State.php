<?php

namespace common\models\staff;

use yii\behaviors\SluggableBehavior;
use yii\db\ActiveRecord;

/**
 *
 * @property int $id [int(11)]
 * @property string $title [varchar(255)]
 * @property string $slug [varchar(255)]
 * @property int $salary [int(11)]
 * @property int $priority [int(11)]
 *
 * @property Employee[] $employees
 */
class State extends ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
                'slugAttribute' => 'slug',
                'ensureUnique' => true,
                'immutable' => true,
            ]
        ];
    }

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title', 'slug'], 'string'],
            [['salary', 'priority'], 'integer'],
        ];
    }

    public function getEmployees()
    {
        return $this->hasMany(Employee::class, ['state_id' => 'id']);
    }
}