<?php

use frontend\models\Staff;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
	'language' => 'ru',
	'timeZone' => 'Asia/Irkutsk',
	'defaultRoute' => "default/index",
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
          ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
	            [
					'class' => \frontend\log\TelegramTarget::class,
		            'levels' => ['error'],
		            'logVars' => [],
	            ]
            ],
        ],
	    'db' => [
		    'class' => 'yii\db\Connection',
		    'dsn' => 'mysql:host=localhost;dbname=natam',
		    'username' => 'garmayev',
		    'password' => 'rhbcnbyfgfrekjdf',
		    'charset' => 'utf8',
	    ],
	    'authManager' => [
		    'class' => 'yii\rbac\DbManager',
		    'defaultRoles' => ['person'],
	    ],
	    'user' => [
		    'class' => 'yii\web\User',
		    'identityClass' => 'app\models\User',
		    'enableSession' => false,
		    //'enableAutoLogin' => true,
	    ],
	    'session' => [ // for use session in console application
		    'class' => 'yii\web\Session'
	    ],
	    'telegram' => [
		'class' => 'aki\telegram\Telegram',
		'botToken' => '1989845524:AAGaba1o5Koc8PTAKuSM6HKFOfdkjTvG8Sc',
		'botUsername' => 'Natam_Trade_bot',
	    ],
	    'i18n' => [
		    'translations' => [
			    'app*' => [
				    'class' => 'yii\i18n\PhpMessageSource',
				    'basePath' => '@backend/messages',
				    'fileMap' => [
					    'app'       => 'app.php',
				    ],
			    ],
		    ],
	    ],
    ],
	'modules' => [
		'user' => [
			'class' => 'dektrium\user\Module',
			'modelMap' => [
				'User' => [
					'class' => \common\models\User::className(),
					'on '.\dektrium\user\models\User::AFTER_REGISTER => function ($e) {
						Yii::error($e);
					}
				]
			]
		],
		'rbac' => [
			'class' => yii2mod\rbac\ConsoleModule::class,
		],
	],
    'params' => $params,
];
