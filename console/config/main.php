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
	'timezone' => 'Asia/Irkutsk',
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
    ],
	'modules' => [
		'user' => [
			'class' => 'dektrium\user\Module',
			'modelMap' => [
				'User' => [
					'class' => \dektrium\user\models\User::className(),
					'on '.\dektrium\user\models\User::AFTER_REGISTER => function ($e) {
						Yii::error($e);
					}
				]
			]
		],
	],
    'params' => $params,
];
