<?php

use common\models\Company;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Companies');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-index">

    <p>
		<?= Html::a(Yii::t('app', 'Create Company'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'summary' => '',
		'columns' => [
			[
				'attribute' => 'title',
				'format' => 'html',
				'value' => function (Company $model) {
					return Html::a($model->title, ['view', 'id' => $model->id]);
				}
			],
			'bik',
			'kpp',
			'ogrn',
            'inn',
			//'boss_id',
			[
				'class' => ActionColumn::className(),
				'urlCreator' => function ($action, Company $model, $key, $index, $column) {
					return Url::toRoute([$action, 'id' => $model->id]);
				}
			],
		],
	]); ?>


</div>
