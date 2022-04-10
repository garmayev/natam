<?php

namespace backend\controllers;

use common\models\Order;
use kartik\mpdf\Pdf;

class AnalyticsController extends BaseController
{
	public function actionIndex()
	{
		$models = Order::find()->all();
		return $this->render('employee', [
			"models" => $models
		]);
	}

	public function actionMonth()
	{
		$models = Order::find()->all();
		return $this->render("month");
	}

	public function actionOrders()
	{
		$models = Order::find()->all();
		return $this->render("orders", [
			"models" => $models
		]);
	}

	public function actionEmployee($startDate = null, $finishDate = null, $export = false)
	{
		if ( is_null($startDate) && is_null($finishDate) ) {
			$models = Order::find()->all();
		} else {
			$models = Order::find()
				->where(['>', 'created_at', \Yii::$app->formatter->asTimestamp($startDate)])
				->andWhere(['<', 'created_at', \Yii::$app->formatter->asTimestamp($finishDate)]);
			\Yii::error($models->createCommand()->getRawSql());
			$models = $models->all();
		}
		if ( isset($_GET['export']) ) {
			$content = $this->renderPartial('employee', [
				'models' => $models,
			]);
			$pdf = new \kartik\mpdf\Pdf([
				'mode' => \kartik\mpdf\Pdf::MODE_UTF8, // leaner size using standard fonts
				'content' => $content,
				'orientation' => Pdf::ORIENT_LANDSCAPE,
				'options' => [
					'class' => 'hidden-print',
				],
				'methods' => [
					'SetTitle' => '',
					'SetHeader' => ['Natam-Trade||'.\Yii::t('app', 'Generated at: {datetime}', ['datetime' => \Yii::$app->formatter->asDatetime(time())])],
					'SetFooter' => ['Страница #{PAGENO}'],
				]
			]);
//			return $content;
			return $pdf->render($content);
		} else {
			return $this->render("employee", [
				"models" => $models,
			]);
		}
	}

	public function actionExportEmployee($startDate, $finishDate)
	{

	}
}