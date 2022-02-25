<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
	'name' => Yii::t('app', 'Natam Trade'),
    'bootstrap' => ['log'],
	'language' => 'ru',
//	'timezone' => 'Asia/Irkutsk',
	'defaultRoute' => "default/index",
    'components' => [
	    'i18n' => [
		    'translations' => [
			    'app*' => [
				    'class' => 'yii\i18n\PhpMessageSource',
				    'basePath' => '@backend/messages',
				    'fileMap' => [
					    'app'       => 'app.php',
					    'natam'     => 'natam.php',
				    ],
			    ],
			    'yii2mod.rbac' => [
				    'class' => 'yii\i18n\PhpMessageSource',
				    'basePath' => '@yii2mod/rbac/messages',
			    ],
		    ],
	    ],
	    'request' => [
            'csrfParam' => '_csrf-frontend',
	        'baseUrl' => '/admin',
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
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
            ],
        ],
	    'authManager' => [
		    'class' => 'yii\rbac\DbManager',
		    'defaultRoles' => ['person'],
	    ],
	    'view' => [
		    'theme' => [
			    'pathMap' => [
				    '@dektrium/user/views' => '@app/views/user',
				    '@yii2mod/rbac/views' => '@app/views/rbac',
			    ],
		    ],
	    ],
	    'formatter' => [
		    'class' => 'yii\i18n\Formatter',
		    'dateFormat' => 'd MMMM Y',
		    'locale' => 'ru-RU',
//		    'timeZone' => 'Asia/Irkutsk',
	    ],
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
		'rbac' => [
			'class' => \yii2mod\rbac\Module::class,
		],
		'staff' => [
			'class' => \garmayev\staff\Module::class,
		]
	],
    'params' => $params,
];
