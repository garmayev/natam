<?php

use common\models\User;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this \yii\web\View */
/* @var $gridViewColumns array */
/* @var $dataProvider \yii\data\ArrayDataProvider */
/* @var $searchModel \yii2mod\rbac\models\search\AssignmentSearch */

$this->title = Yii::t('yii2mod.rbac', 'Assignments');
$this->params['breadcrumbs'][] = $this->title;
$this->render('/layouts/_sidebar');
?>
<div class="assignment-index">

	<?php Pjax::begin(['timeout' => 5000]); ?>

	<?php echo GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'summary' => '',
		'columns' => [
			[
				'attribute' => 'username',
				'format' => 'html',
				'value' => function (User $model) {
					return Html::a($model->name, ['view', 'id' => $model->id]);
				}
			],
		],
	]); ?>

	<?php Pjax::end(); ?>
</div>
