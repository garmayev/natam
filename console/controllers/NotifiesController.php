<?php

namespace console\controllers;

use console\helper\Helper;
use frontend\models\Order;
use frontend\models\OrderProduct;
use frontend\models\Telegram;
use frontend\models\Updates;
use frontend\modules\admin\models\Settings;
use garmayev\staff\models\Employee;
use yii\httpclient\Client;

/**
 * @property Updates $currentUpdate
 * @property Employee $currentEmployee
 */
class NotifiesController extends \yii\console\Controller
{
	private $helper;
	private $settings;
	private $currentUpdate;
	private $currentEmployee;
	private $logMessage = "";

	const ERROR_NO_EMPLOYEE = 0;
	const ERROR_STAGE_TIMEOUT = 1;

	const CHECK_NONE = 0;
	const CHECK_NEXT = 1;
	const CHECK_CHEF = 2;

	public function init()
	{
		$this->helper = new Helper();
		$this->settings = Settings::findOne(["name" => "notify"]);
		$this->settings = $this->settings->getContent()["notify"];
		\Yii::$app->params["notify"] = $this->settings;
		parent::init();
	}

	public function actionIndex()
	{
		$orders = Order::find()->where(["<", "status", Order::STATUS_COMPLETE])->all();
		$this->logMessage .= "Открытых заказов: ".count($orders)."\n";
		echo "Открытых заказов: ".count($orders)."\n";
		foreach ($orders as $order)
		{
			switch ($this->check($order)) {
				case self::CHECK_NONE:
					$this->logMessage .= "\tИнформацию о заказе #{$order->id} отправлять не надо\n";
					echo "\tИнформацию о заказе #{$order->id} отправлять не надо\n";
					break;
				case self::CHECK_NEXT:
					$this->notify($order);
					break;
				case self::CHECK_CHEF:
					$this->toChef($order);
					break;
			}
		}
		Helper::error($this->logMessage);
	}

	/**
	 * Проверка необходимости отправки сообщения сотруднику
	 *
	 * @param Order $order
	 * @return false
	 */
	private function check(Order $order)
	{
		/**
		 * TODO: 1. Ограничить отправку "тревожных" сообщений
		 */
		/**
		 * TODO: 2. Запретить повторный круг рассылки по сотрудникам
		 * Вариант 1: В таблице updates ввести поле, наподобие type = enum('chef', 'employee')
		 *              По умолчанию значение равно employee. После отправки сообщения начальству 0 chef
		 */
		$now = time();
		$update = Updates::find()->where(["order_id" => $order->id])->andWhere(["order_status" => $order->status])->orderBy("created_at DESC")->one();
		if ( isset($update) ) {
			$this->logMessage .= "\tИнформация о заказе #{$order->id} уже отправлялась пользователю {$update->employee->getFullname()}\n";
			echo "\tИнформация о заказе #{$order->id} уже отправлялась пользователю {$update->employee->getFullname()}\n";
			$this->currentUpdate = $update;
			$chef = Helper::searchChef($this->currentUpdate, $this->settings);
			if ( isset($update->chef) && !isset($chef) && $update->chef->chat_id === $chef ) {
				$this->logMessage .= "\tПересылка не требуется\n";
				echo "\tПересылка не требуется\n";
				return self::CHECK_NONE;
			}
			if ( $update->created_at + $this->settings["limit"][$order->status - 1] < $now ) {
				$this->logMessage .= "\tТребуется уведомление начальства о задержке на этапе {$order->getStatus($order->status)}\n";
				echo "\tТребуется уведомление начальства о задержке на этапе {$order->getStatus($order->status)}\n";
				return self::CHECK_CHEF;
			}
			$this->logMessage .= "\tНеобходима пересылка другому сотруднику\n";
			echo "\tНеобходима пересылка другому сотруднику\n";
			return self::CHECK_NEXT;
		}
		$this->logMessage .= "\tИнформация о данном заказе еще никому не отправлялась\n";
		echo "\tИнформация о данном заказе еще никому не отправлялась\n";
		return self::CHECK_NEXT;
	}

	/**
	 * Подготовка к отправке уведомления
	 *
	 * @param Order $order
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\httpclient\Exception
	 */
	private function notify(Order $order)
	{
		$employee = $this->searchEmployee($order);
		if ( isset($employee) )
		{
			if ( isset($employee->chat_id) ) {
				$this->logMessage .= "Отправка уведомления пользователю #{$employee->getFullname()}\n";
				echo "Отправка уведомления пользователю #{$employee->getFullname()}\n";
				$this->currentEmployee = $employee;
				if ( isset($this->currentUpdate) ) {
					$order->notify_started_at = $employee->id;
					$order->save();
				}
				$response = $this->sendMessage([
					"chat_id" => $employee->chat_id,
					"text" => $this->generateTextMessage($order),
					"reply_markup" => json_encode($this->generateKeyboard($order))
				]);
				$this->newUpdate($order, $response);
				$employee->last_message_at = time();
				$employee->save();
			}
		} else {
			$this->logMessage .= "\tНе удалось найти сотрудника\n";
			echo "\tНе удалось найти сотрудника\n";
			$this->toChef($order, self::ERROR_NO_EMPLOYEE);
		}
	}

	/**
	 * Поиск сотрудника
	 *
	 * @param Order $order
	 * @return Employee|null
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\httpclient\Exception
	 */
	private function searchEmployee($order)
	{
		/**
		 * @var $employee Employee
		 */
		$employee = Employee::find()->where(["state_id" => $order->status])->orderBy("last_message_at ASC")->one();
		if ( $employee ) {
			if ($order->notify_started_at !== $employee->id) {
				return $employee;
			} else {
				$this->toChef($order);
			}
		}
		return null;
	}

	/**
	 * Отправка уведомления начальству
	 *
	 * @param Order $order
	 * @param int $status
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\httpclient\Exception
	 */
	private function toChef(Order $order, $status = self::ERROR_STAGE_TIMEOUT)
	{
		$text = "";
		if ( isset($this->currentUpdate) ) {
			$chef = Helper::searchChef($this->currentUpdate, $this->settings);
			switch ($status) {
				case self::ERROR_NO_EMPLOYEE:
					$text = "Не найден свободный сотрудник для обработки заказа #{$order->id} на стадии '{$order->getStatus($order->status)}'";
					break;
				case self::ERROR_STAGE_TIMEOUT:
					$text = "За отведенное время никто не ответил на заявку по заказу #{$order->id}";
					break;
			}
			if ( $order->boss_chat_id !== $chef ) {
				$this->sendMessage(["chat_id" => $chef, "text" => $text]);
				$order->boss_chat_id = $chef;
				$order->save();
			}
		}
	}

	/**
	 * Отправка сообщения сотруднику
	 *
	 * @param $args
	 * @return \yii\httpclient\Response
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\httpclient\Exception
	 */
	public function sendMessage($args)
	{
		$response = Telegram::sendMessage($args);
		if ( isset($this->currentUpdate) ) {
			$this->closeUpdate();
			if ( $response->isOk && !$response->getData()["ok"] ) {
				Helper::error([
					"Ошибка при изменении сообщения для пользователя {$this->currentEmployee->getFullname()}",
					$response->getData()
				], true);
			}
		}
		return $response;
	}

	/**
	 * Закрытие экземпляра Updates
	 *
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\httpclient\Exception
	 */
	private function closeUpdate()
	{
		$this->logMessage .= "Изменение сообщения для пользователя {$this->currentUpdate->employee->getFullname()}\n";
		$this->currentUpdate->updated_at = time();
		$this->currentUpdate->save();
		$response = Telegram::editMessage([
			"chat_id" => $this->currentUpdate->employee->chat_id,
			"text" => "Информация о заказе #{$this->currentUpdate->order_id} была отправлена другому пользователю",
			"message_id" => $this->currentUpdate->message_id]);
		if ( $response->isOk && !$response->getData()["ok"] ) {
			Helper::error([
				"Ошибка при изменении сообщения для пользователя {$this->currentUpdate->employee->getFullname()}",
				$response->getData()
			], true);
		}
	}

	/**
	 * Создание экземпляра Updates
	 *
	 * @param $order
	 * @param $response
	 */
	private function newUpdate($order, $response)
	{
		$result = $response->getData();
		$this->logMessage .= "Создание сообщения пользователю {$this->currentEmployee->getFullname()}\n";
		$update = new Updates([
			"order_id" => $order->id,
			"order_status" => $order->status,
			"employee_id" => $this->currentEmployee->id,
			"message_id" => $result["result"]["message_id"],
			"message_timestamp" => $result["result"]["date"],
		]);
		if (!$update->save()) {
			\Yii::error($update->getErrorSummary(true));
		}
	}

	/**
	 * Подготовка текстового сообщения в Telegram
	 *
	 * @param $order
	 * @return string
	 */
	private function generateTextMessage($order)
	{
		return "{$this->generateHeader($order)}{$this->generateClientInfo($order)}{$this->generateProductList($order)}";
	}

	/**
	 * Подготовка заголовка для текстового сообщения в Telegram
	 *
	 * @param Order $order
	 * @return string
	 */
	private function generateHeader($order)
	{
		$text = "";
		switch ($order->status) {
			// Новый заказ (заказ отправляется менеджерам)
			case 0:
				$text .= "Новый заказ #{$order->id}\n\n";
				break;
			// Подготовлен для отправки (заказ отправляется кладовщикам)
			case 1:
				$text .= "Заказ #{$order->id} одобрен\n\n";
				break;
			// В процессе доставки (заказ отправляется водителям)
			case 2:
				$text .= "Заказ #{$order->id} ожидаеь доставки\n\n";
				break;
		}
		return $text;
	}

	/**
	 * Подготовка информации о клиенте для текствового сообщения в Telegram
	 *
	 * @param $order
	 * @return string
	 */
	private function generateClientInfo($order)
	{
		/**
		 * @var $order Order
		 */
		$text = "Информация о клиенте\n";
		if (isset($order->client->name)) {
			$text .= "\tФИО: {$order->client->name}\n";
		}
		if (isset($order->client->phone)) {
			$text .= "Контактный номер: {$order->client->phone}\n\n";
		}
		return $text;
	}

	/**
	 * Подготовка списка продуктов в заказе для текстового сообщения в Telegram
	 *
	 * @param Order $order
	 * @return string
	 */
	private function generateProductList($order)
	{
		/**
		 * @var OrderProduct $order_product
		 */
		$text = "Список заказанных продуктов: \n";
		$total_price = 0;
		foreach ($order->orderProduct as $order_product) {
			$product = $order_product->product;
			$text .= "\t{$product->title}\n" .
				"\t\tОбъем: {$product->value}\n" .
				"\t\tКоличество: {$order_product->product_count}\n" .
				"\t\tЦена: {$product->price}\n\n";
			$total_price += $order_product->product_count * $product->price;
		}
		$text .= "Общая стоимость заказа: {$total_price}";
		return $text;
	}

	/**
	 * Подготовка кнопок для текстового сообщения в Telegram
	 *
	 * @param $order
	 * @return array
	 */
	private function generateKeyboard($order)
	{
		return ["inline_keyboard" => [[
			["text" => "Выполнено", "callback_data" => "/order_complete id={$order->id}"],
			["text" => "Отложить", "callback_data" => "/order_hold id={$order->id}"]
		]]];
	}

	public function actionTest()
	{
		var_dump( $this->check(Order::findOne(139)) );
	}
}