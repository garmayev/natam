<?php

namespace frontend\controllers;

use common\models\Category;
use common\models\Client;
use common\models\Order;
use common\models\staff\Employee;
use common\models\TelegramMessage;
use frontend\commands\Command;
use frontend\models\Telegram;
use frontend\models\Updates;
use Yii;
use yii\data\ActiveDataProvider;
use yii\hepers\Url;
use yii\rest\Controller;
use yii\web\Response;

/**
 *
 */
class TelegramController extends Controller
{
    public static function start($telegram, $args = null)
    {
        if (isset($args)) {
            if (isset($telegram->input->message)) {
                Yii::error($telegram->input->message->chat->id);
                if (Yii::$app->user->isGuest) {
                    $client = Client::findOne(["phone" => array_keys($args)[0]]);
                    if ($client) {
                        $client->chat_id = $telegram->input->message->chat->id;
                        $client->save();
                        $user = $client->user;
                        Yii::$app->user->switchIdentity($user, 0);
                    }
                }
                $telegram->sendMessage([
                    'chat_id' => $telegram->input->message->chat->id,
                    "text" => "Здравствуйте! Вас приветствует сервис повторного заказа компании Натам-Трейд!",
                    "reply_markup" => json_encode([
                        "keyboard" => [
                            [
                                ["text" => "/all_orders"]
                            ]
                        ],
                        "resize_keyboard" => true,
                    ])
                ]);
            }
        }

        if (self::checkPermission($telegram, "employee")) {
            $orders = Order::find()->where(["<", "status", Order::STATUS_COMPLETE])->all();
        } else {
            $client = Client::findOne(["user_id" => Yii::$app->user->id]);
            $orders = Order::find()->where(["client_id" => $client->id])->all();
        }
        if (count($orders)) {
            $keyboard = [];
            foreach ($orders as $order) {
                $keyboard[] = [
                    ["text" => "Заказ #{$order->id} Стоимость: {$order->totalPrice}", "callback_data" => "/order order_id={$order->id}"]
                ];
            }
            // \Yii::error($keyboard);
            $chat_id = isset($telegram->input->message) ? $telegram->input->message->chat->id : $telegram->input->callback_query->from["id"];
//            return $keyboard;
            $telegram->sendMessage([
                'chat_id' => $chat_id,
                "text" => "Список ваших заказов\n\nНажмите на соответствующий заказ для получения более развернутой информации",
                "reply_markup" => json_encode([
                    "inline_keyboard" => $keyboard
                ]),
            ]);
        } else {
            $telegram->sendMessage([
                'chat_id' => $telegram->input->message->chat->id,
                "text" => "Заказов нет!\nСперва создайте заказ на сайте https://natam03.ru/",
            ]);
        }
    }

    protected static function checkPermission($telegram, $permission)
    {
        if (!Yii::$app->user->can($permission)) {
            $message = isset($telegram->input->message) ? $telegram->input->message : $telegram->input->callback_query;
            $result = $telegram->sendMessage([
                'chat_id' => $message->chat->id,
                "text" => Yii::t("telegram", "You don`t have permissions for this action")
            ]);
            return false;
        }
        return true;
    }

    public static function orders($telegram, $args = null)
    {
        if (self::checkPermission($telegram, "employee")) {
            $orders = Order::find()->where(["<", "status", Order::STATUS_COMPLETE])->all();
        } else {
            $client = Client::findOne(["user_id" => Yii::$app->user->id]);
            $orders = Order::find()->where(["client_id" => $client->id])->all();
        }
        if (count($orders)) {
            $keyboard = [];
            foreach ($orders as $order) {
                $keyboard[] = [
                    ["text" => "Заказ #{$order->id} Стоимость: {$order->totalPrice}", "callback_data" => "/order order_id={$order->id}"]
                ];
            }
            $chat_id = isset($telegram->input->message) ? $telegram->input->message->chat->id : $telegram->input->callback_query->from["id"];
            if (isset($telegram->input->message)) {
                $telegram->sendMessage([
                    'chat_id' => $chat_id,
                    "text" => "Список ваших заказов!\n\nНажмите на соответствующий заказ для получения более развернутой информации",
                    "reply_markup" => json_encode([
                        "inline_keyboard" => $keyboard
                    ]),
                ]);

            } else {
                $telegram->editMessageText([
                    "chat_id" => $chat_id,
                    "message_id" => $telegram->input->callback_query->message['message_id'],
                    "text" => "Список ваших заказов\n\nНажмите на соответствующий заказ для получения более развернутой информации",
                    "reply_markup" => json_encode([
                        "inline_keyboard" => $keyboard
                    ])
                ]);
            }
        } else {
            $telegram->sendMessage([
                'chat_id' => $telegram->input->message->chat->id,
                "text" => "Заказов нет!\nСперва создайте заказ на сайте https://natam03.ru/",
            ]);
        }
    }

    public static function view($telegram, $args = null)
    {
        if (isset($args["order_id"])) {
            $order = Order::findOne($args["order_id"]);
//			\Yii::error($order->generateTelegramText());
            $telegram->editMessageText([
                "message_id" => $telegram->input->callback_query->message["message_id"],
                'chat_id' => $telegram->input->callback_query->message["chat"]["id"],
                "text" => $order->generateTelegramText(),
                "parse_mode" => "html",
                "reply_markup" => json_encode([
                    "inline_keyboard" => [
                        [
                            ["text" => "Повторить заказ", "callback_data" => "/copy order_id={$order->id}"],
                        ], [
                            ["text" => "Перейти на сайт", "url" => "https://" . Yii::$app->params['hostName'] . "/"],
                        ], [
                            ["text" => "Назад", "callback_data" => "/all_orders"],
                        ]
                    ]
                ]),
            ]);
        }
    }

    public static function copy($telegram, $args = null)
    {
        if (isset($args["order_id"])) {
            $order = Order::findOne($args["order_id"]);
            $last = Order::find()->where(["client_id" => $order->client_id])->orderBy(['id' => SORT_DESC])->one();
            if ($last->created_at < (time() - 5)) {
                $copy = $order->deepClone();
                $telegram->editMessageText([
                    "message_id" => $telegram->input->callback_query->message["message_id"],
                    'chat_id' => $telegram->input->callback_query->message["chat"]["id"],
                    "text" => "Ваш заказ успешно повторен!",
                    "parse_mode" => "html",
                    "reply_markup" => json_encode([
                        "inline_keyboard" => [
                            [
                                ["text" => "Назад", "callback_data" => "/all_orders"],
                            ]
                        ]
                    ]),
                ]);
            }
        }
    }

    public static function inlineCheck($telegram, $args = null)
    {
        if (self::checkPermission($telegram, "employee")) {
            if (isset($args)) {
                $text = "Check complete!\n\n" . json_encode($args);
            } else {
                $text = "Check complete!";
            }
            $result = $telegram->editMessageText([
                'message_id' => $telegram->input->callback_query->message["message_id"],
                'chat_id' => $telegram->input->callback_query->message["chat"]["id"],
                "text" => $text,
            ]);
        }
    }

    public static function manager($telegram, $args = null)
    {
        /**
         * $args = [ "id" => order_id ]
         */
        if (self::checkPermission($telegram, "employee")) {
            if (isset($args)) {
                $order = Order::findOne($args["id"]);
                $order->status = Order::STATUS_PREPARED;
                $order->boss_chat_id = null;
                if (!$order->save()) {
                    Yii::error($order->getErrorSummary(true));
                }
            }
        } else {
            $text = Yii::t("telegram", "You don`t have permissions for this action");
            Yii::error($text);
        }
    }

    public static function store($telegram, $args = null)
    {
        /**
         * $args = [ "id" => order_id ]
         */
        if (self::checkPermission($telegram, "employee")) {
            if (isset($args)) {
                $order = Order::findOne($args["id"]);
                if (isset($order->delivery_type) && $order->delivery_type === Order::DELIVERY_STORE) {
                    $employee = Employee::findOne($args["driver_id"]);
                    $order->status = Order::STATUS_DELIVERY;
                    $order->boss_chat_id = null;
                    if (!$order->save()) {
                        Yii::error($order->getErrorSummary(true));
                    } else {
                        TelegramMessage::send($employee, $order);
                        if (!empty($order->client->chat_id)) {
                            Yii::$app->telegram->sendMessage([
                                'chat_id' => $order->client->chat_id,
                                'text' => "По вашему заказу #$order->id ожидайте доставку в течении 2-х часов",
                                'parse_mode' => 'HTML'
                            ]);
                        }
                    }
                } else {
                    $messages = TelegramMessage::find()
                        ->where(["status" => TelegramMessage::STATUS_OPENED])
                        ->andWhere(["order_id" => $order->id])
                        ->all();
                    foreach ($messages as $message) $message->hide();
                    $order->status = Order::STATUS_COMPLETE;
                    Yii::error($order->save());
                }
            } else {
                Yii::error("Unknown order");
            }
        } else {
            $text = Yii::t("telegram", "You don`t have permissions for this action");
            Yii::error($text);
        }
    }

    public static function driver($telegram, $args = null)
    {
        /**
         * $args = [ "id" => order_id ]
         */
        if (self::checkPermission($telegram, "employee")) {
            if (isset($args)) {
                $order = Order::findOne($args["id"]);
                $messages = TelegramMessage::find()
                    ->where(['order_id' => $order->id])
                    ->andWhere(['status' => TelegramMessage::STATUS_OPENED])
                    ->all();

                foreach ($messages as $message) {
                    $message->hide();
                }
                $order->status = Order::STATUS_COMPLETE;
                $order->boss_chat_id = null;
                if (!$order->save()) {
                    Yii::error($order->getErrorSummary(true));
                }
            }
        } else {
            $text = Yii::t("telegram", "You don`t have permissions for this action");
            Yii::error($text);
        }
    }

    public static function startgame($telegram, $args = [])
    {
        $telegram->sendMessage([
            "chat_id" => $telegram->input->message->chat->id,
            "text" => "Start Game?",
            "reply_markup" => json_encode([
                "inline_keyboard" => [
                    [["text" => "Personal", "callback_data" => "game=personal"]]
                ]
            ])
        ]);
    }

    public static function game($telegram, $args = [])
    {
        Yii::error("send game url?");
        $telegram->sendGame([
            "chat_id" => $telegram->input->callback_query->message["chat"]["id"],
            "game_short_name" => "personal",
            "reply_markup" => json_encode([
                "inline_keyboard" => [
                    [["text" => "Start", "url" => "https://t.me/share/url?url=https://natam03.ru/telegram/game"]]
                ]
            ])
        ]);
    }

    public static function alert($telegram, $args = null)
    {
        if (isset($args)) {
            $order = Order::findOne($args["id"]);
            $alerts = TelegramMessage::find()->where(['type' => TelegramMessage::TYPE_NOTIFY])->andWhere(['order_id' => $order->id])->all();
            foreach ($alerts as $alert) {
                $alert->hide();
            }
        }
    }

    public function beforeAction($action)
    {
        if ($action->id === "telegram") {
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($data["callback_query"])) {
                $user = $this->findUser($data["callback_query"]["from"]["id"]);
            } elseif (isset($data["message"])) {
                $user = $this->findUser($data["message"]["from"]["id"]);
                if (!empty($user)) {
                    Yii::$app->user->switchIdentity($user, 0);
                    $this->enableCsrfValidation = false;
                    return parent::beforeAction($action);
                } else {
                    if (preg_match('/(\/start)/', $data['message']['text'])) {
                        return parent::beforeAction($action);
                    } else {
                        $user = $this->findUser($data["message"]["from"]["id"]);
                    }
                }
            }
            if (empty($user)) {
                $this->asJson([
                    'code' => 'ERROR',
                    'message' => 'You are not logged',
                ]);
                Yii::error([
                    "input_data" => $data,
                    "message" => 'user is not logged',
                ]);
                return false;
            }
            Yii::$app->user->switchIdentity($user, 0);
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    protected function findUser($chat_id)
    {
        $employee = Employee::find()->where(["chat_id" => $chat_id])->one();
        if ($employee) {
            return $employee->user;
        } else {
            $client = Client::findOne(['chat_id' => $chat_id]);
            if ($client) {
                return Client::findOne(["chat_id" => $chat_id])->user;
            } else {
                return null;
            }
            // $user = Client::findOne(["chat_id" => $chat_id])->user;
        }
        // return $user;
    }

    public function actionCallback()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        $telegram = new Telegram($input);
        if (isset($telegram->message)) {
            $text = $telegram->message["text"];
            $args = explode(" ", $text);
            $command = array_shift($args);
            switch ($command) {
                case "/start":
                    if (count($args) > 0) {
                        $client = Client::findOne(["phone" => $args[0]]);
                    } else {
                        $client = Client::findOne(["chat_id" => $telegram->message["from"]["id"]]);
                    }
                    if (isset($client)) {
                        $client->chat_id = $telegram->message["from"]["id"];
                        $client->save();

                        $text = "Список ваших заказов";
                        $text .= (count($client->orders) === 0) ? " пуст" : ":";
                        $markup = ["inline_keyboard" => []];
                        foreach (Order::find()->where(["client_id" => $client->id])->all() as $order) {
                            $markup["inline_keyboard"][] = [["text" => "Заказ #$order->id", "callback_data" => "/order $order->id"]];
                        }
                        if (Telegram::sendMessage(["chat_id" => $client->chat_id, "text" => $text, "reply_markup" => json_encode($markup)])) {
                            return ["ok" => true];
                        }
                    } else {
                        if (isset($args[0])) {
                            $staff = Employee::find()->where(["phone" => $args[0]])->orWhere(["chat_id" => $telegram->message["from"]["id"]])->one();
                            if ($staff) {
                                if (is_null($staff->chat_id)) {
                                    $staff->chat_id = $telegram->message["from"]["id"];
                                    $staff->save();
                                }
                                Telegram::sendMessage([
                                    "chat_id" => $staff->chat_id,
                                    "text" => "В этот бот вам будут приходить новые заявки с сайта " . Yii::$app->name,
                                ]);
                            }
                        }
                    }
                    break;
                case "/all_orders":
                    $client = Client::findOne(['user_id' => Yii::$app->user->id]);
                    $orders = Order::find()->where(['client_id' => $client->id])->andWhere(["<", "status", Order::STATUS_COMPLETE])->all();
                    $telegram->send();
                    break;
            }
        } else if (isset($telegram->callback_query)) {
            $text = $telegram->callback_query["data"];
            $args = explode(" ", $text);
            $command = array_shift($args);
            switch ($command) {
                case "/start":
                    $client = Client::find()->where(["phone" => $args[0]])->orWhere(["chat_id" => $telegram->callback_query["from"]["id"]])->one();
                    if (isset($client)) {
                        $client->chat_id = $telegram->callback_query["from"]["id"];
                        $client->save();

                        $text = "Список ваших заказов";
                        $text .= (count($client->orders) === 0) ? " пуст" : ":";
                        $markup = ["inline_keyboard" => []];
                        foreach (Order::find()->where(["client_id" => $client->id])->andWhere(["<", "status", Order::STATUS_COMPLETE])->all() as $order) {
                            $markup["inline_keyboard"][] = [["text" => "Заказ #$order->id", "callback_data" => "/order $order->id"]];
                        }
                        if (Telegram::editMessage(["chat_id" => $client->chat_id, "text" => $text, "reply_markup" => json_encode($markup), "message_id" => $telegram->callback_query["message"]["message_id"]])) {
                            return ["ok" => true];
                        }
                    }
                    break;
                case "/order":
                    $order = Order::findOne($args[0]);
                    $client = Client::find()->where(["chat_id" => $telegram->callback_query["from"]["id"]])->one();
                    $text = "Заказ #$order->id\nАдрес доставки: $order->address\nДата создания заказа: " . Yii::$app->formatter->asDate($order->created_at, "php: d M Y H:i") . "\nСодержимое заказа:\n";
                    foreach ($order->products as $product) {
                        $text .= "\t$product->title\n\t\t$product->value\n\t\t$product->price\n";
                    }
                    $text .= "\nОбщая стоимость заказа: $order->price";
                    $markup = ["inline_keyboard" => [
                        [
                            [
                                "text" => "Повторить этот заказ",
                                "callback_data" => "/reply $order->id"
                            ]
                        ],
                        [
                            ["text" => Yii::t("app", "Cancel"), "callback_data" => "/start $client->phone"]
                        ]
                    ]];
                    if (Telegram::editMessage(["chat_id" => $client->chat_id, "text" => $text, "reply_markup" => json_encode($markup), "message_id" => $telegram->callback_query["message"]["message_id"]])) {
                        return ["ok" => true];
                    }
                    break;
                case "/reply":
                    $order = Order::findOne($args[0]);
                    $copy = $order->deepClone();
                    $client = $copy->client;
                    $copy->created_at = time();
                    $copy->status = Order::STATUS_NEW;
                    $copy->save();
                    break;
                case "/order_complete":
                    parse_str($args[0], $argument);
                    $order = Order::findOne($argument["id"]);
                    $staff = Employee::find()->where(["chat_id" => $telegram->callback_query["from"]["id"]])->one();
// \Yii::error(json_encode($order));
// \Yii::error(json_encode($staff));
                    if (isset($staff) && isset($order) && ($staff->state_id == $order->status)) {
                        $updates = Updates::find()->where(["order_id" => $order->id])->andWhere(["order_status" => $order->status])->all();
                        if (count($updates)) {
                            foreach ($updates as $update) {
                                Telegram::editMessage(["chat_id" => $update->employee->chat_id, "message_id" => $update->message_id, "text" => "Статус заказа #{$order->id} был изменен"]);
                                $update->delete();
                            }
                        } else {
                            Telegram::editMessage([
                                "message_id" => $telegram->callback_query["message"]["message_id"],
                                "text" => "Статус заказа #{$order->id} был изменен",
                                "chat_id" => $telegram->callback_query["from"]["id"],
                            ]);
                        }
                        $order->status++;
                        $order->save();
                    }
                    $order->status = $staff->state_id + 1;
                    $order->save();
                    break;
                case "/order_driver":
                    parse_str($args[0], $argument);
                    $order = Order::findOne($argument["order_id"]);

                    if (isset($argument["driver_id"])) {
                        $driver = Employee::findOne($argument["driver_id"]);

                        Yii::error($driver->attributes);

                        $order->status = Order::STATUS_DELIVERY;
                        $order->save();

                        TelegramMessage::send($driver, $order);
                    } else {
                        $order->status = Order::STATUS_COMPLETE;
                        $order->save();
                    }

                    break;
                case "/order_hold":
                    parse_str($args[0], $argument);
                    $order = Order::findOne($argument["id"]);
                    if (isset($order)) {
                        Telegram::editMessage([
                            "message_id" => $telegram->callback_query["message"]["message_id"],
                            "chat_id" => $telegram->callback_query["from"]["id"],
                            "text" => "Выберите время для задержки",
                            "reply_markup" => json_encode([
                                "inline_keyboard" => [
                                    [
                                        [
                                            "text" => "Отложить на 1 час",
                                            "callback_data" => "/order_hold_by_time id={$order->id}&sec=3600",
                                        ]
                                    ], [
                                        [
                                            "text" => "Отложить на 3 час",
                                            "callback_data" => "/order_hold_by_time id={$order->id}&sec=10800",
                                        ]
                                    ], [
                                        [
                                            "text" => "Отложить на 6 час",
                                            "callback_data" => "/order_hold_by_time id={$order->id}&sec=21600",
                                        ]
                                    ], [
                                        [
                                            "text" => "Отложить на сутки",
                                            "callback_data" => "/order_hold_by_time id={$order->id}&sec=86400",
                                        ]
                                    ],
                                ]
                            ]),
                        ]);
                    }
                    break;
                case "/order_hold_by_time":
                    parse_str($args[0], $argument);
                    $order = Order::findOne($argument["id"]);
                    $seconds = $argument["sec"];
                    if (isset($order)) {
                        $order->hold($seconds);
                        $updates = Updates::find()->where(["order_id" => $order->id])->andWhere(["order_status" => $order->status])->all();
                        switch ($seconds) {
                            case 3600:
                                $text = "Заказ #$order->id отложен на 1 час\n";
                                break;
                            case 10800:
                                $text = "Заказ #$order->id отложен на 3 часа\n";
                                break;
                            case 21600:
                                $text = "Заказ #$order->id отложен на 6 часов\n";
                                break;
                            case 86400:
                                $text = "Заказ #$order->id отложен на сутки\n";
                                break;
                        }
                        foreach ($updates as $update) {
                            Telegram::editMessage([
                                "message_id" => $update->message_id,
                                "text" => "{$text}Адрес доставки: $order->address\nДата создания заказа: " . Yii::$app->formatter->asDate($order->created_at, "php: d M Y H:i") . "\nСодержимое заказа:\n",
                                "chat_id" => $update->employee->chat_id,
                                "reply_markup" => json_encode(["inline_keyboard" => [
                                    [["text" => "Кладовщику", "callback_data" => "/order_restore id={$order->id}"]]
                                ]]),
                            ]);
                        }
                    }
                    break;
                case "/order_restore":
                    parse_str($args[0], $argument);
                    $order = Order::findOne($argument["id"]);
                    $staff = Employee::find()->where(["chat_id" => $telegram->callback_query["from"]["id"]])->one();
                    if (isset($staff) && isset($order) && ($staff->state_id == $order->status)) {
                        $updates = Updates::find()->where(["order_id" => $order->id])->andWhere(["order_status" => $order->status])->all();
                        foreach ($updates as $update) {
                            Telegram::editMessage(["chat_id" => $update->employee->chat_id, "message_id" => $update->message_id, "text" => "Статус заказа #{$order->id} был изменен"]);
                            // $update->delete();
                        }
                        $order->status = Order::STATUS_PREPARED;
                        $order->save();
                    }
                    break;
            }
        }
        return ["ok" => false];
    }

    public function actionTelegram()
    {
        $telegram = Yii::$app->telegram;
        Command::run("/start", [$this, "start"]);
        Command::run("/manager", [$this, "manager"]);
        Command::run("/store", [$this, "store"]);
        Command::run("/driver", [$this, "driver"]);

        Command::run("/all_orders", [$this, "orders"]);
        Command::run("/order", [$this, "view"]);
        Command::run("/copy", [$this, "copy"]);
        Command::run("/startgame", [$this, "startgame"]);
        Command::run("game=personal", [$this, "game"]);
        Command::run("/alert", [$this, "alert"]);
//		Command::run("/status_store", [$this, "status_store"]);
    }

    public function actionGame()
    {
        Yii::$app->response->format = Response::FORMAT_HTML;
        // $this->layout = "_blank";
        return $this->renderPartial('game', [
            'models' => new ActiveDataProvider([
                'query' => Category::find()->where(['main' => 1])
            ]),
        ]);
    }

    public function actionTest()
    {
        Yii::$app->response->format = Response::FORMAT_HTML;
        return $this->renderPartial('test');
    }
}
