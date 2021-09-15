<?php

namespace frontend\modules\admin\controllers;

use dektrium\user\models\User;
use yii\data\ActiveDataProvider;

class UserController extends BaseController
{
	public function actionIndex()
	{
		return $this->render("index", [
			"userProvider" => new ActiveDataProvider([
				"query" => User::find()
			])
		]);
	}

	public function actionView($id)
	{
		$model = User::findOne($id);
		return $this->render("view", [
			"model" => $model
		]);
	}
}