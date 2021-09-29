<?php

use frontend\models\Order;

return [
    'adminEmail' => 'admin@example.com',
	'notify' => [
		'limit' => [
			Order::STATUS_NEW => 300,             // Количество секунд для Первого этапа обработки заказа  (менеджеры)
			Order::STATUS_PREPARE => 3600,        // Количество секунд для Второго этапа обработки заказа  (кладовщики)
			Order::STATUS_DELIVERY => 3600        // Количество секунд для Третьего этапа обработки заказа (водители)
		],
		'alert' => [
			[
				"chat_id" => 435190684,
				"time" => 780
			], [
				"chat_id" => 443353023,
				"time" => 1200,
			], [
				"chat_id" => 581330380,
				"time" => 900
			]
		],
	],
	'telegram' => [
		"bot_id" => '1989845524:AAGaba1o5Koc8PTAKuSM6HKFOfdkjTvG8Sc',
//		'manager' => [443353023, /* 581330380 */]
	],
];
