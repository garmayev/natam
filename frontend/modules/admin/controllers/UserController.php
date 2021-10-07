<?php

namespace frontend\modules\admin\controllers;

use frontend\models\Staff;
use frontend\models\User;
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

	public function actionCreate()
	{
		$model = new User();
		if ( \Yii::$app->request->isPost ) {
			$model->setScenario('register');
			$userLoad = $model->load(\Yii::$app->request->post());
			$model->confirmed_at = time();
			if ( $userLoad && $model->register() ) {
				$staff = new Staff();
				$staff->user_id = $model->id;
				if ( $staff->load(\Yii::$app->request->post()) && $staff->save() ) {
					return $this->redirect('/admin/user/index');
				} else {
					\Yii::error("Staff is not saved");
					\Yii::error($staff->getErrorSummary(true));
				}
			} else {
				\Yii::error("User is not saved");
				\Yii::error($model->getErrorSummary(true));
			}
		}
		return $this->render('create', [
			"model" => $model,
			'staff' => new Staff(),
		]);
	}

	public function actionUpdate($id)
	{
		$model = User::findOne($id);
		if ( \Yii::$app->request->isPost ) {
			if ( $model->load(\Yii::$app->request->post()) && $model->save() ) {
				if ($model->staff->load(\Yii::$app->request->post()) && $model->staff->save()) {
					return $this->redirect('/admin/user/index');
				} else {
					\Yii::error('Staff is not saved');
					\Yii::error($model->staff->getErrorSummary(true));
				}
			} else {
				\Yii::error('User is not saved');
				\Yii::error($model->getErrorSummary(true));
			}
		}
		return $this->render('create', [
			"model" => $model,
			'staff' => $model->staff,
		]);
	}

	public function actionDelete($id)
	{
		$model = User::findOne($id);
		if ( $model ) {
			$model->delete();
		}
		return $this->redirect('/admin/user/index');
	}
}