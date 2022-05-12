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
			$models->andWhere(['>', 'created_at', \Yii::$app->formatter->asTimestamp($from_date . " 00:00")]);
		}
		if (!is_null($to_date)) {
			$models->andWhere(['<', 'created_at', \Yii::$app->formatter->asTimestamp($to_date . " 23:59")]);
		}
		if ($export) {
			if (ob_get_length()) ob_end_clean();
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="' . urlencode("export_orders_{$from_date}_{$to_date}") . '.xls"');
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
			$orders->andWhere(['>', 'created_at', \Yii::$app->formatter->asTimestamp($from_date . ' 00:00')]);
		}
		if (!is_null($to_date)) {
			$orders->andWhere(['<', 'created_at', \Yii::$app->formatter->asTimestamp($to_date . ' 23:59')]);
		}
		if ($export) {
			if (ob_get_length()) ob_end_clean();
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="' . urlencode("export_employee_{$from_date}_{$to_date}") . '.xls"');
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
			$result[$model->id] = ['model' => $model];
			$telegram_message = TelegramMessage::find()
				->where(['order_id' => $model->id])
				->all();
			foreach ($telegram_message as $message) {
				if ( $message->status === TelegramMessage::STATUS_CLOSED ) {
					$result[$model->id]['details'][] = [
						'created' => $message->createdBy,
						'updated' => $message->updatedBy,
						'status' => $message->order_status,
						'elapsed' => \Yii::$app->formatter->asRelativeTime($message->updated_at, $message->created_at),
					];
				} else {
					$result[$model->id]['details'][] = [
						'created' => $message->createdBy,
						'status' => $message->order_status,
						'elapsed' => null,
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
		$sheet->setCellValue('B1', \Yii::t('app', 'Created By'));
		$sheet->setCellValue('C1', \Yii::t('app', 'Elapsed by "New Order"'));
		$sheet->setCellValue('D1', \Yii::t('app', 'Created By'));
		$sheet->setCellValue('E1', \Yii::t('app', 'Elapsed by "Order Prepared"'));
		$sheet->setCellValue('F1', \Yii::t('app', 'Created By'));
		$sheet->setCellValue('G1', \Yii::t('app', 'Elapsed by "Order Delivered"'));
		foreach ($data as $key => $item) {
			$sheet->setCellValue("A{$row}", $item['model']->id);
			foreach ($item['details'] as $index => $detail) {
				\Yii::error($detail);
				switch ($detail['status']) {
					case Order::STATUS_NEW:
						if (isset($detail) && isset($detail['elapsed'])) {
							if ( isset($detail['updated']) ) {
								$sheet->setCellValue("B{$row}", $detail['updated']->employee->getFullName());
							} else {
								$sheet->setCellValue("B{$row}", $detail['created']->employee->getFullName());
							}
							$sheet->setCellValue("C{$row}", $detail['elapsed']);
						}
						break;
					case Order::STATUS_PREPARE:
						if (isset($detail) && isset($detail['elapsed'])) {
							if ( isset($detail['updated']) ) {
								$sheet->setCellValue("D{$row}", $detail['updated']->employee->getFullName());
							} else {
								$sheet->setCellValue("D{$row}", $detail['created']->employee->getFullName());
							}
							$sheet->setCellValue("E{$row}", $detail['elapsed']);
						}
						break;
					case Order::STATUS_DELIVERY:
						if (isset($detail) && isset($detail['elapsed'])) {
							if ( isset($detail['updated']) ) {
								$sheet->setCellValue("F{$row}", $detail['updated']->employee->getFullName());
							} else {
								$sheet->setCellValue("F{$row}", $detail['created']->employee->getFullName());
							}
							$sheet->setCellValue("G{$row}", $detail['elapsed']);
						}
						break;
				}
			}
//				$item[$key]['details'][$i] = [
//
//				];
//			foreach ($item[$key]['details'] as $order_detail) {
//
//			}
//			$sheet->setCellValue("C{$row}", (isset($item['opened'])) ? $item['opened']->getFullname() : '');
//			$sheet->setCellValue("D{$row}", (isset($item['closed'])) ? $item['closed']->getFullname() : '');
//			$sheet->setCellValue("E{$row}", \Yii::$app->formatter->asDatetime($item['created_at']));
//			$sheet->setCellValue("F{$row}", (isset($item['updated_at'])) ? \Yii::$app->formatter->asDatetime($item['updated_at']) : '');
//			$sheet->setCellValue("G{$row}", (isset($item['updated_at'])) ? \Yii::$app->formatter->asRelativeTime($item['updated_at'], $item['created_at']) : '');
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
		foreach ($data as $key => $item) {
			$sheet->setCellValue("A{$row}", $item['employee']->getFullName());
			$sheet->setCellValue("B{$row}", $item['completed_messages']);
			$sheet->setCellValue("C{$row}", $item['uncompleted_messages']);
			$sheet->setCellValue("D{$row}", $item['total_messages']);
			$row++;
		}
		return \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
	}
}