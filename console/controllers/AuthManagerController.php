<?php

namespace console\controllers;

use yii\helpers\Console;

class AuthManagerController extends \yii\console\Controller
{
	public function actionGetAssignments($search, $role)
	{
		$user = $this->findUser($search);
		print_r($user->attributes);
		echo "\n";
	}

	public function actionIndex()
	{
		echo "Test";
	}

	public function actionRun($command)
	{
		if ( self::runConsole("composer show -i", "/(dektrium\/yii2-user)/m", $status) ) {
			echo "Module dektrium/yii2-user is installed";
		} else {
			self::runConsole("composer require dektrium/yii2-user", null, $status);
		}
		if ( self::runConsole("composer show -i", "/(yii2mod\/yii2-rbac)/m", $status) ) {
			echo "Module yii2mod/yii2-rbac is installed";
		}
		echo "\n";
	}

	protected static function runConsole($command, $pattern = null, &$status)
	{
		$handler = popen($command, 'r');
		while (!feof($handler)) {
			if ( !is_null($pattern) && preg_match($pattern, fgets($handler), $matches) ) {
				$status = pclose($handler);
				return true;
			}
		}
		$status = pclose($handler);
		return false;
	}


	protected function findUser($key)
	{
		$component = \Yii::$app->getModule("user");
		if (isset($component)) {
//			print_r($component->modelMap["User"]);
//			$object = new Object($component->modelMap["User"]);
			$model = $component->modelMap["User"];
			$user = $model::findOne($key);
			if ($user) {
				return $user;
			}
			$user = $model::find()->where(["username" => $key])->one();
			if ($user) {
				return $user;
			}
			$user = $model::find()->where(["email" => $key])->one();
			if ($user) {
				return $user;
			}

			$this->stdout("Can not find any user", Console::FG_RED);
			return false;
		} else {
//			$component = \Yii::$app->getComponents("user");
		}
	}
}