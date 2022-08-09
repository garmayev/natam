<?php

use common\models\staff\Employee;
use yii\web\View;
use yii\widgets\DetailView;
use yii\helpers\Html;

/**
 * @var $this View
 * @var $model Employee
 */

echo "<p>";
echo Html::a(Yii::t("user", "Profile"), ["user/profile/show", "id" => $model->user_id], ["class" => ["btn", "btn-primary"], "style" => "margin-right: 10px;"]);
echo Html::a(Yii::t("app", "Update"), ["staff/update", "id" => $model->id], ["class" => ["btn", "btn-success"]]);
echo "</p>";

echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'name',
        'family',
        'phone',
        [
            'attribute' => 'birthday',
            "format" => "date",
            "value" => function (Employee $model) {
                return (!empty($model->birthday)) ? $model->birthday : null;
            }
        ],
        [
            'attribute' => 'state.title',
        ],
        [
            "attribute" => "chat_id",
        ]
    ]
]);