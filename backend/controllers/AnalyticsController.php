<?php

namespace backend\controllers;

use common\models\Order;
use common\models\Settings;
use common\models\TelegramMessage;
use common\models\staff\Employee;
use kartik\mpdf\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet;
use SebastianBergmann\CodeCoverage\Report\Xml\Totals;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class AnalyticsController extends BaseController
{
	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
				'denyCallback' => function () {
					Url::remember(Url::current());
					return $this->redirect(['user/security/login']);
				}
			],
		];
	}

	public function actionOrders($from_date = null, $to_date = null, $filter = null, $export = null)
	{
		$models = Order::find();
		if (!is_null($from_date)) {
			$models->andWhere(['>', 'created_at', \Yii::$app->formatter->asTimestamp($from_date . " 00:00")]);
		}
		if (!is_null($to_date)) {
			$models->andWhere(['<', 'created_at', \Yii::$app->formatter->asTimestamp($to_date . " 23:59")]);
		}
		$models->orderBy(['id' => SORT_DESC]);
		if ($export) {
			if (ob_get_length()) ob_end_clean();
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="' . urlencode("export_orders_{$from_date}_{$to_date}") . '.xls"');
			$writer = $this->createSpreadByOrder($models->all());
			$writer->save('php://output');
			die;
		}
		return $this->render("orders", [
			"models" => $models->all(),
		]);
	}

	public function actionOrdersByStatus($from_date = null, $to_date = null, $employee = null, $expired = null)
	{
		if (!is_null($from_date)) {
			$fromTimestamp = \Yii::$app->formatter->asTimestamp($from_date . " 00:00");
		}
		if (!is_null($to_date)) {
			$toTimestamp = \Yii::$app->formatter->asTimestamp($to_date . " 23:59");
		}
		$employee = Employee::findOne($employee);
		$user = $employee->user;
		$messages = TelegramMessage::find()
			->where([">", 'updated_at', $fromTimestamp])
			->andWhere(["<", "updated_at", $toTimestamp])
			->andWhere(["updated_by" => $user->id])
			->andWhere(["chat_id" => $employee->chat_id])
			->andWhere(["order_status" => $employee->state_id]);
		if ( $expired ) {
			$messages->andWhere(['>', '`updated_at` - `created_at`', Settings::getInterval($employee->state_id - 1)]);
		} else {
			$messages->andWhere(['<', '`updated_at` - `created_at`', Settings::getInterval($employee->state_id - 1)]);
		}
		return $this->render("orders_by_status", [
			"messages" => $messages->all(),
		]);
	}

	public function actionEmployee($from_date = null, $to_date = null, $filter = null, $export = null)
	{
		$employees = Employee::find()->orderBy(['state_id' => SORT_ASC])->all();
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

    public function actionFuel($from_date = null, $to_date = null, $filter = null)
    {
        $employees = Employee::find()->where(["state_id" => 3])->all();
        return $this->render("fuel", [
            "employees" => $employees,
        ]);
    }

	protected function getDataByOrder($models)
	{
		$result = [];
		foreach ($models as $model) {
			$result[$model->id] = ['model' => $model, 'details' => []];
			$telegram_message = TelegramMessage::find()
				->where(['order_id' => $model->id])
				->all();
			if (count($telegram_message)) {
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
		}
		return $result;
	}

	protected function getDataByEmployee($models, $orders)
	{
		$result = [];
		foreach ($models as $model) {
			$query = TelegramMessage::find()
				->where(['updated_by' => $model->user_id])
				->andWhere(['in', 'order_id', ArrayHelper::getColumn($orders, 'id')])
				->groupBy('order_id');
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
				'percent' => (count($completed_messages) > 0) ? (count($completed_messages) / count($total_messages)) * 100 : 0,
			];
		}
		return $result;
	}

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    protected function createSpreadByOrder($models)
	{
		$spreadsheet = new Spreadsheet();
		$data = $this->getDataByOrder($models);
		$row = 2;
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Номер заказа');
		$sheet->setCellValue('B1', \Yii::t('app', 'Created By'));
		$sheet->setCellValue('C1', \Yii::t('app', 'Elapsed by "{status}"', ['status' => Order::getStatusList()[Order::STATUS_NEW]]));
		$sheet->setCellValue('D1', \Yii::t('app', 'Created By'));
		$sheet->setCellValue('E1', \Yii::t('app', 'Elapsed by "{status}"', ['status' => Order::getStatusList()[Order::STATUS_PREPARED]]));
		$sheet->setCellValue('F1', \Yii::t('app', 'Created By'));
		$sheet->setCellValue('G1', \Yii::t('app', 'Elapsed by "{status}"', ['status' => Order::getStatusList()[Order::STATUS_DELIVERY]]));
		foreach ($data as $key => $item) {
			if (count($item['model']->messages)) {
				$sheet->setCellValue("A{$row}", $item['model']->id);
				foreach ($item['details'] as $index => $detail) {
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
						case Order::STATUS_PREPARED:
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
			$row++;
			}
		}
		return \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'xls');
	}

	protected function createSpreadByEmployee($models, $orders)
	{
		$spreadsheet = new Spreadsheet();
		$data = $this->getDataByEmployee($models, $orders);
		$row = 2;
		$sheet = $spreadsheet->getActiveSheet();
		$argb = [
			[
				"fill" => [
					"fillType" => Fill::FILL_SOLID,
					"startColor" => [
						"argb" => "99EEEEEE"
					],
				]
			],
			[
				"fill" => [
					"fillType" => Fill::FILL_SOLID,
					"startColor" => [
						"argb" => "99DFF0D8"
					],
				]
			],
			[
				"fill" => [
					"fillType" => Fill::FILL_SOLID,
					"startColor" => [
						"argb" => "99DAEDF7"
					],
				]
			],
			[
				"fill" => [
					"fillType" => Fill::FILL_SOLID,
					"startColor" => [
						"argb" => "99FCF8E3"
					],
				]
			],
		];
		$sheet->getDefaultRowDimension()->setRowHeight(17);
		$sheet->setCellValue('A1', "ФИО сотрудника");
		$sheet->getColumnDimension('A')->setWidth(20);
		$sheet->setCellValue('B1', "Выполненые в срок");
		$sheet->getColumnDimension('B')->setWidth(20);
		$sheet->setCellValue('C1', "Невыполненые в срок");
		$sheet->getColumnDimension('C')->setWidth(25);
		$sheet->setCellValue('D1', "Всего действий");
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->setCellValue('E1', "Процент успешности");
		$sheet->getColumnDimension('E')->setWidth(25);
		foreach ($data as $key => $item) {
//			\Yii::error($argb[$item['employee']->state_id]);
			$sheet->getStyle("A{$row}:E{$row}")->applyFromArray($argb[$item['employee']->state_id]);
			$sheet->setCellValue("A{$row}", $item['employee']->getFullName());
			$sheet->setCellValue("B{$row}", $item['completed_messages']);
			$sheet->setCellValue("C{$row}", $item['uncompleted_messages']);
			$sheet->setCellValue("D{$row}", $item['total_messages']);
			$sheet->setCellValue("E{$row}", \Yii::$app->formatter->asPercent($item['percent'] / 100, 2));
			$row++;
		}
		return \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
	}
}