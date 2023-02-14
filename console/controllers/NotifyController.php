<?php

namespace console\controllers;

use common\models\Order;
use common\models\TelegramMessage;
use frontend\models\Updates;
use frontend\modules\admin\models\Settings;
use garmayev\staff\models\Employee;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class NotifyController extends Controller
{
    private $settings;

    public function init()
    {
        $settings = Settings::findOne(["name" => "notify"]);
        Yii::$app->params["notify"] = $settings->getContent()["notify"];
        $this->settings = $settings->getContent()["notify"];
        parent::init();
    }

    public function actionIndex()
    {
        $models = Order::find()->where(["<", "status", Order::STATUS_DELIVERY])->all();

//        if (!$this->checkHours()) {
        foreach ($models as $model) {
            /* if ( $this->isNeedNextMessage($model) ) {
                $this->stdout("\tТребуется отправка сообщения сотруднику\n");
                $employee = $this->findNextEmployee($model);
                if ( $employee ) {
                    if (isset($employee->family)) {
                        $this->stdout("\tДля уведомления был выбран сотрудник {$employee->family} {$employee->name}\n");
                    } else {
                        $this->stdout("\tДля уведомления был выбран сотрудник {$employee->id}\n");
                    }
                    $this->sendMessage($employee, $model);
                    $employee->last_message_at = time();
                    $employee->save();
                } else {
                    $this->stdout("\tНе найден подходящий сотрудник для уведомления\n");
                }
            } else {
                $this->stdout("\tОтправка уведомления не требуется");
            } */
            if ($this->isNeedAlert($model)) {
                $level = $this->findAlertLevel($model);
                if (YII_ENV === 'prod') {
                    TelegramMessage::send(null, $model, $level);
                } else {
                    $this->stdout("Уровень тревоги: $level\n");
                    $this->stdout("Заказ #{$model->id}\n", Console::BOLD);
                    $this->stdout("\tТребуется отправка сообщения начальнику\n");
                }
            }
        }
//        }
    }

    /**
     * @param Order $model
     * @return bool
     */
    protected function isNeedAlert($model)
    {
        $timeout = (time() - ($model->created_at + $this->settings["alert"][$model->status - 1]["time"]) > 0);
        if (!$timeout) {
            return false;
        }
        if ($lastMessage = $model->lastMessage) {
            if ($lastMessage->type === TelegramMessage::TYPE_ALERT) {
                $this->stdout($lastMessage->type);
                if (time() - ($lastMessage->created_at + 900))
                    return true;
            }
            return false;
        }
        return $timeout;
    }

    protected function findAlertLevel(Order $model)
    {
        $lastMessage = $model->lastMessage;
        if (isset($lastMessage)) {
            if ($lastMessage->type === TelegramMessage::TYPE_NOTIFY || $lastMessage->type === null) {
                return 0;
            }
        } else {
            return 0;
        }
        return 1;
    }

    public function auth()
    {

    }

    public function actionHide()
    {
        $messages = TelegramMessage::find()->where(['status' => TelegramMessage::STATUS_OPENED])->all();
        foreach ($messages as $message) $message->hide();
    }

    protected function checkHours()
    {
        $hour = intval(Yii::$app->formatter->asDatetime(time(), "H"));
        $weekDay = date('w', time());
        if (($weekDay != 0)) {
            if ($weekDay != 6) {
                if ($hour > 8 && $hour < 17) return true;
            } else {
                if ($hour > 8 && $hour < 13) return true;
            }
        }
        return false;
    }

    /**
     * @param $model
     * @return Employee|null
     */
    protected function findNextEmployee($model)
    {
        $usedEmployees = [];
        $updates = Updates::find()->where(["order_id" => $model->id])->andWhere(["order_status" => $model->status])->all();
        if (count($updates) > 1) {
            foreach ($updates as $update) {
                $usedEmployees[] = $update->employee->id;
            }
        }
        return Employee::find()
            ->where(["not in", "id", $usedEmployees])
            ->andWhere(["state_id" => $model->status])
            ->orderBy(["last_message_at" => SORT_ASC])
            ->all();
    }

    /**
     * @param Order $model
     * @return int
     */
    protected function isNeedNextMessage($model)
    {
        $update = Updates::find()->where(["order_id" => $model->id])->andWhere(["order_status" => $model->status])->orderBy(["created_at" => SORT_DESC])->one();
        if ($update) {
            if (!is_null($model->hold_at)) {
                if (time() > ($hold = $model->hold_at + $model->hold_time)) {
                    return 1;
                }
                $this->stdout("\tЗаказ был отложен менеджером! Осталось " . ($hold - time()) . " секунд\n");
                return 0;
            } else {
                if (time() < $update->created_at + $this->settings["limit"][$model->status - 1]) {
                    return 0;
                } else {
                    return 1;
                }
            }
        }
        return 2;
    }

    /**
     * @param $employee
     * @param Order $model
     * @return bool
     */
    protected function sendMessage($employees, $model)
    {
        foreach ($employees as $employee) {
            /*			$response = Telegram::sendMessage(["chat_id" => $employee->chat_id, "text" => $model->generateTelegramText(), "parse_mode" => "HTML", "reply_markup" => json_encode(["inline_keyboard" => $model->generateTelegramKeyboard()])]);
                        if ( !$response->isOk ) {
                            echo "\t\tChat ID: {$employee->chat_id}\n\t\tText: {$model->generateTelegramText()}";
                            return false;
                        }
                        $data = $response->getData();
                        $update = new Updates([
                            "order_id" => $model->id,
                            "order_status" => $model->status,
                            "employee_id" => $employee->id,
                            "message_id" => $data["result"]["message_id"],
                            "message_timestamp" => $data["result"]["date"],
                        ]);
                        $update->save(); */
            $this->stdout($employee->chat_id);
        }
        return true;
    }
}
