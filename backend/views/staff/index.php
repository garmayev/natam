<?php

use common\models\search\EmployeeSearch;
use common\models\staff\Employee;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var $this View
 * @var $employeeProvider ActiveDataProvider
 * @var $searchModel EmployeeSearch
 */

$this->title = Yii::t('app', 'Staff');
$this->params['breadcrumbs'][] = $this->title;

echo Html::beginTag("p");
echo Html::a(Yii::t("app", "Create Employee"), ['staff/create-employee'], ['class' => ['btn', 'btn-success'], 'style' => 'margin-right: 10px;']);
echo Html::a(Yii::t("app", "Create State"), ['staff/create-state'], ['class' => ['btn', 'btn-primary']]);
echo Html::endTag("p");

echo GridView::widget([
    "dataProvider" => $employeeProvider,
    'filterModel' => $searchModel,
    "summary" => "",
    "columns" => [
        [
            "attribute" => "fullname",
            "format" => "html",
            "value" => function (Employee $model) {
                return Html::a($model->fullname, ["staff/view-employee", "id" => $model->id]);
            }
        ],
        [
            "attribute" => "phone",
            "format" => "html",
            "value" => function (Employee $model) {
                return Html::a("+{$model->phone}", "tel:+{$model->phone}");
            }
        ],
        [
            "attribute" => "state_id",
            "label" => Yii::t("app", "State"),
            "format" => "raw",
            "filterAttribute" => 'state_name',
            "value" => function (Employee $model) {
                return Html::a($model->state->title, ['staff/view-state', 'id' => $model->state_id]);
            }
        ], [
            "class" => ActionColumn::class,
            'template' => '{view} {delete}',
            'buttons' => [
                'view' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                        'title' => Yii::t('app', 'lead-view'),
                    ]);
                },

                'update' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                        'title' => Yii::t('app', 'lead-update'),
                    ]);
                },
                'delete' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                        'title' => Yii::t('app', 'lead-delete'),
                    ]);
                }

            ],
            'urlCreator' => function ($action, Employee $model, $key, $index) {
                if ($action === 'view') {
                    return Url::to(["/staff/view-employee", "id" => $model->id]);
                }

                if ($action === 'delete') {
                    return Url::to(["/staff/delete-employee", "id" => $model->id]);
                }

            }
        ]
    ]
]);