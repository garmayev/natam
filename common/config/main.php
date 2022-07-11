<?php
return [
	'aliases' => [
		'@bower' => '@vendor/bower-asset',
		'@npm' => '@vendor/npm-asset',
		'@webroot' => '@app/web',
	],
	'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
	'components' => [
		'cache' => [
			'class' => 'yii\caching\FileCache',
		],
		'telegram' => [
			'class' => 'aki\telegram\Telegram',
			'botToken' => '1989845524:AAGaba1o5Koc8PTAKuSM6HKFOfdkjTvG8Sc',
			'botUsername' => '@Natam_Trade_Test_bot',
		]
	],
	'modules' => [
		'user' => [
			'class' => 'dektrium\user\Module',
			'enableRegistration' => false,
			'enableConfirmation' => false,
			'rememberFor' => 9676800,
			'modelMap' => [
				'User' => \common\models\User::className(),
			],
		],
	],
];
