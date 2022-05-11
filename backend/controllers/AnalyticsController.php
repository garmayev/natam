<?php

namespace backend\controllers;

use common\models\Order;
use common\models\Settings;
use common\models\TelegramMessage;
use garmayev\staff\models\Employee;
use kartik\mpdf\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class AnalyticsController extends BaseController
{
	public function actionOrders($from_date = null, $to_date = null, $filter = null, $export = null)
	{
		$models = Order::find();
		if (!is_null($from_date)) {
			$models->andWhere(['>', 'created_at', \Yii::$app->formatter->asTimestamp($from_date." 00:00")]);
		}
		if (!is_null($to_date)) {
			$models->andWhere(['<', 'created_at', \Yii::$app->formatter->asTimestamp($to_date." 23:59")]);
		}
		if ($export) {
			if (ob_get_length()) ob_end_clean();
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="'. urlencode("export_orders_{$from_date}_{$to_date}").'.xls"');
			$writer = $this->createSpreadByOrder($models->all());
			$writer->save('php://output');
			die;
		} else {
			return $this->render("orders", [
				"models" => $models->all()
			]);
		}
	}

	public function actionEmployee($from_date = null, $to_date = null, $filter = null, $export = null)
	{
		$employees = Employee::find()->all();
		$orders = Order::find();
		if (!is_null($from_date)) {
			$orders->andWhere(['>', 'created_at', \Yii::$app->formatter->asTimestamp($from_date.' 00:00')]);
		}
		if (!is_null($to_date)) {
			$orders->andWhere(['<', 'created_at', \Yii::$app->formatter->asTimestamp($to_date.' 23:59')]);
		}
		if ( $export ) {
			if (ob_get_length()) ob_end_clean();
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="'. urlencode("export_employee_{$from_date}_{$to_date}").'.xls"');
			$writer = $this->createSpreadByEmployee($employees, $orders->all());
			$writer->save('php://output');
			die;
		} else {
			return $this->render('employee', [
				'models' => $employees,
				'orders' => $orders->all(),
			]);
		}
	}

	protected function getDataByOrder($models)
	{
		$result = [];
		foreach ($models as $model) {
			foreach (Order::getStatusList() as $key => $status) {
				$telegram_message = TelegramMessage::find()
					->where(['order_id' => $model->id])
					->andWhere(['order_status' => $key])
					->one();
				if (!empty($telegram_message)) {
					$result[] = [
						'model' => $model->id,
						'status' => $telegram_message->order_status,
						'opened' => ($key == Order::STATUS_NEW) ? $model->client : $telegram_message->createdBy->employee,
						'closed' => ($telegram_message->updatedBy) ? $telegram_message->updatedBy->employee : null,
						'created_at' => $telegram_message->created_at,
						'updated_at' => $telegram_message->updated_at,
					];
				}
			}
		}
		return $result;
	}

	protected function getDataByEmployee($models, $orders)
	{
		$result = [];
		foreach ($models as $model) {
			$query = TelegramMessage::find()
				->where(['updated_by' => $model->user_id])
				->andWhere(['in', 'order_id', ArrayHelper::getColumn($orders, 'id')]);
			$total_messages = (clone $query)
				->all();
			$completed_messages = (clone $query)
				->andWhere(['<', '`updated_at` - `created_at`', Settings::getInterval($model->state_id - 1)])
				->all();
			$uncompleted_messages = (clone $query)
				->andWhere(['>', '`updated_at` - `created_at`', Settings::getInterval($model->state_id - 1)])
				->all();
			$result[] = [
				'employee' => $model,
				'completed_messages' => count($completed_messages),
				'uncompleted_messages' => count($uncompleted_messages),
				'total_messages' => count($total_messages),
			];
		}
		return $result;
	}

	protected function createSpreadByOrder($models)
	{
		$spreadsheet = new Spreadsheet();
		$order = new Order();
		$data = $this->getDataByOrder($models);
		$row = 2;
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Номер заказа');
		$sheet->setCellValue('B1', \Yii::t('app', 'Status'));
		$sheet->setCellValue('C1', \Yii::t('app', 'Created By'));
		$sheet->setCellValue('D1', \Yii::t('app', 'Updated By'));
		$sheet->setCellValue('E1', \Yii::t('app', 'Created At'));
		$sheet->setCellValue('F1', \Yii::t('app', 'Updated At'));
		$sheet->setCellValue('G1', \Yii::t('app', 'Elapsed Time'));
		foreach ($data as $key => $item)
		{
			$sheet->setCellValue("A{$row}", $item['model']);
			$sheet->setCellValue("B{$row}", $order->getStatus($item['status']));
			$sheet->setCellValue("C{$row}", (isset($item['opened'])) ? $item['opened']->getFullname() : '');
			$sheet->setCellValue("D{$row}", (isset($item['closed'])) ? $item['closed']->getFullname() : '');
			$sheet->setCellValue("E{$row}", \Yii::$app->formatter->asDatetime($item['created_at']));
			$sheet->setCellValue("F{$row}", (isset($item['updated_at'])) ? \Yii::$app->formatter->asDatetime($item['updated_at']) : '');
			$sheet->setCellValue("G{$row}", (isset($item['updated_at'])) ? \Yii::$app->formatter->asRelativeTime($item['updated_at'], $item['created_at']) : '');
			$row++;
		}
		return \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
	}

	protected function createSpreadByEmployee($models, $orders)
	{
		$spreadsheet = new Spreadsheet();
		$data = $this->getDataByEmployee($models, $orders);
		$row = 2;
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', "ФИО сотрудника");
		$sheet->setCellValue('B1', "Выполненые в срок");
		$sheet->setCellValue('C1', "Невыполненые в срок");
		$sheet->setCellValue('D1', "Всего действий");
		foreach ($data as $key => $item)
		{
			$sheet->setCellValue("A{$row}", $item['employee']->getFullName());
			$sheet->setCellValue("B{$row}", $item['completed_messages']);
			$sheet->setCellValue("C{$row}", $item['uncompleted_messages']);
			$sheet->setCellValue("D{$row}", $item['total_messages']);
			$row++;
		}
		return \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
	}
}