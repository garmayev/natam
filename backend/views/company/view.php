<?php

use common\models\Client;
use common\models\Company;
use common\models\Order;
use common\models\search\OrderSearch;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Company */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Companies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="company-view">

    <p>
		<?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
		<?php
		if (Yii::$app->user->can('employee')) echo Html::a(Yii::t('app', 'Join coworker'), ['join', 'id' => $model->id], ['class' => 'btn btn-warning']);
		?>
    </p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'id',
			'title',
			'bik',
			'kpp',
			'ogrn',
			[
				'attribute' => 'boss_id',
				'format' => 'html',
				'value' => function (Company $model) {
					return Html::a($model->boss->name, ['client/view', 'id' => $model->boss_id]);
				}
			],
		],
	]) ?>

	<?= GridView::widget([
		'dataProvider' => new ArrayDataProvider([
			'allModels' => $model->workers
		]),
		'summary' => '',
		'columns' => [
			[
				'attribute' => 'name',
				'format' => 'html',
				'value' => function (Client $model) {
					return Html::a($model->name, ['client/view', 'id' => $model->id]);
				}
			], [
				'attribute' => 'phone',
				'format' => 'html',
				'value' => function (Client $model) {
					return Html::a($model->phone, "tel:+{$model->phone}");
				}
			], [
				'attribute' => 'email',
				'format' => 'html',
				'value' => function (Client $model) {
					if ($model->email) {
						return Html::a($model->email, "mailto:$model->email");
					}
					return Html::tag('span', Yii::t('yii', '(not set)'), ['class' => 'not-set']);
				}
			], [
				'attribute' => 'chat_id',
				'format' => 'html',
				'value' => function (Client $model) {
					if ($model->chat_id) {
						return Html::tag('span', '', ['class' => ['glyphicon', 'glyphicon-ok']]);
					} else {
						return Html::tag('span', '', ['class' => ['glyphicon', 'glyphicon-remove']]);
					}
				}
			], [
				'class' => ActionColumn::className(),
				'urlCreator' => function ($action, $client, $key, $index) use ($model) {
					switch ($action) {
						case 'delete':
							return ['unjoin', 'id' => $model->id, 'client_id' => $client->id];
							break;
					}
					return [$action, 'id' => $model->id, 'hvost' => time()];
				},
				'template' => '{delete}',
			]
		]
	]) ?>

	<?php
    $orderSearch = new OrderSearch();
    if ( Yii::$app->user->can('employee') ) {

	    $orders = Order::find()->innerJoinWith('client')->innerJoinWith('location')->where(['in', 'client_id', ArrayHelper::map($model->workers, 'id', 'id')])->orderBy(['id' => SORT_DESC]);
    } elseif ( $model->boss_id === (Client::findOne(['user_id' => Yii::$app->user->id]))->id ) {
	    $orders = Order::find()->innerJoinWith('client')->innerJoinWith('location')->where(['in', 'client_id', ArrayHelper::map($model->workers, 'id', 'id')])->orderBy(['id' => SORT_DESC]);
    } else {
        $orders = Order::find()->innerJoinWith('client')->innerJoinWith('location')->where(['client_id' => (Client::findOne(['user_id' => Yii::$app->user->id]))->id])->orderBy(['id' => SORT_DESC]);
    }

	echo GridView::widget([
		'dataProvider' => new ActiveDataProvider([
			'query' => $orders
		]),
		'summary' => '',
		'columns' => [
			[
				'attribute' => 'id',
				'format' => 'html',
				'value' => function (Order $model) {
					return Html::a("#{$model->id}", ['order/view', 'id' => $model->id]);
				}
			],
			[
				'attribute' => 'client.name',
				'format' => 'html',
				'value' => function (Order $model) {
					return Html::a($model->client->name, ['client/view', 'id' => $model->client_id]);
				}
			],
			[
				'attribute' => 'client.phone',
				'format' => 'html',
				'value' => function (Order $model) {
					return Html::a($model->client->phone, "tel:+{$model->client->phone}");
                }
			],
			[
				'attribute' => 'location.title',
				'format' => 'html',
				'value' => function (Order $model) {
                    return Html::a($model->location->title, ['location/view', 'id' => $model->location_id]);
				}
			],
            'price',
			'created_at:datetime', 'delivery_date:datetime'
		]
	])
	?>
</div>
