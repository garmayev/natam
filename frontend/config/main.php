<?php

use frontend\models\Staff;

$params = array_merge(
	require __DIR__ . '/../../common/config/params.php',
	require __DIR__ . '/../../common/config/params-local.php',
	require __DIR__ . '/params.php',
	require __DIR__ . '/params-local.php'
);

return [
	'id' => 'app-frontend',
	'basePath' => dirname(__DIR__),
	'bootstrap' => ['log'],
	'controllerNamespace' => 'frontend\controllers',
	'name' => 'Натам Трейд',
	'timeZone' => 'Asia/Irkutsk',
	'language' => "ru",
	'components' => [
		'view' => [
			'theme' => [
				'pathMap' => [
					'@dektrium/user/views' => '@app/views/user',
					'@garmayev/staff/views' => '@app/modules/admin/views/layouts/main',
				]
			]
		],
		'request' => [
			'csrfParam' => '_csrf-frontend',
			'baseUrl' => '',
		],
		'session' => [
			// this is the name of the session cookie used for login on the frontend
			'name' => 'advanced-frontend',
		],
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
					'except' => [
						'yii\web\HttpException:404',
					]
				],
			],
		],
		'errorHandler' => [
			'errorAction' => 'site/error',
		],
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
			'rules' => [
				'/' => 'site/index',
				'<action:\w+>' => 'site/<action>',
				'<controller:\w+>/<action:\w+>' => '<controller>/<action>'
			],
		],
		'i18n' => [
			'translations' => [
				'app*' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'fileMap' => [
						'app' => 'app.php',
						'app/error' => 'error.php',
						'natam' => 'natam.php',
					],
				],
				'natam' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'fileMap' => [
						'natam' => 'natam.php',
					],
				],
				'telegram' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'fileMap' => [
						'telegram' => 'telegram.php',
					],
				]
			],
		],
		'cart' => [
			'class' => 'devanych\cart\Cart',
			'storageClass' => 'devanych\cart\storage\DbSessionStorage',
			'calculatorClass' => 'devanych\cart\calculators\SimpleCalculator',
			'params' => [
				'key' => 'cart',
				'expire' => 604800,
				'productClass' => \common\models\Product::class,
				'productFieldId' => 'id',
				'productFieldPrice' => 'price',
			],
		],
		'formatter' => [
			'defaultTimeZone' => 'Asia/Irkutsk',
			'dateFormat' => 'php:d-m-Y',
			'datetimeFormat' => 'php:d-m-Y в H:i:s',
			'timeFormat' => 'php:H:i:s',
		],
		'authManager' => [
			'class' => 'yii\rbac\DbManager',
			'defaultRoles' => ['person'],
		],
	],
	'modules' => [
		'gridview' => [
			'class' => '\kartik\grid\Module',
		],
		'user' => [
			'class' => 'dektrium\user\Module',
			'modelMap' => [
				'User' => \common\models\User::className(),
			],
			'controllerMap' => [
				'settings' => [
					'class' => \dektrium\user\controllers\SettingsController::className(),
					'layout' => '//../../modules/admin/views/layouts/main'
				],
				'registration' => [
					'class' => \dektrium\user\controllers\RegistrationController::className(),
					'on ' . \dektrium\user\controllers\RegistrationController::EVENT_AFTER_REGISTER => function ($e) {
						Yii::error($e->form->email);
						$user = \dektrium\user\models\User::find()->where(["email" => $e->form->email])->one();
						$staff = new Staff(["user_id" => $user->id]);
						$staff->save();
					}
				]
			]
		],
		'staff' => [
			'class' => 'garmayev\staff\Module',
			'user_class' => '\common\models\User',
			'urlPrefix' => 'staff',
			'layout' => '@app/modules/admin/views/layouts/main',
		],
        'api' => [
            'class' => \frontend\modules\api\Module::class,
        ]
	],
	'params' => $params,
];
