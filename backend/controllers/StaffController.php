<?php

namespace backend\controllers;

use common\models\staff\Employee;
use common\models\staff\State;
use yii\data\ActiveDataProvider;
use yii\web\Response;

class StaffController extends BaseController
{
	public function actionIndex()
	{
		return $this->render("index", [
			"employeeProvider" => new ActiveDataProvider([
				"query" => Employee::find()->orderBy(['state_id' => SORT_ASC])
			])
		]);
	}

	public function actionUpdate($id)
	{
		$model = Employee::findOne($id);
		if ( \Yii::$app->request->isPost ) {
			if ( $model->load(\Yii::$app->request->post()) && $model->save() ) {
				\Yii::$app->session->setFlash("success", \Yii::t("app", "Employee info is successfullt saved"));
				return $this->redirect(['index']);
			} else {
				\Yii::error($model->getErrorSummary(true));
				\Yii::$app->session->setFlash("error", \Yii::t("app", "Employee info is not saved"));
			}
		}
		$units = $this->cars();
		\Yii::$app->response->format = Response::FORMAT_HTML;
		return $this->render('update', [
			"model" => $model,
			"units" => $units,
		]);
	}

	public function cars()
	{
		$units = [];
		$token = \Yii::$app->runAction('cars/login')["SessionId"];
		$ids = \Yii::$app->runAction('cars/units', ['token' => "$token"]);
		foreach ( $ids["Units"] as $id ) {
			$units[$id["UnitId"]] = $id["Name"];
		}
		return $units;
	}

	public function actionCreateEmployee()
	{
		$model = new Employee();
		if ( \Yii::$app->request->isPost ) {
			if ( $model->load(\Yii::$app->request->post()) && $model->save() ) {
				\Yii::$app->session->setFlash("success", \Yii::t("app", "Employee info is successfullt saved"));
				return $this->redirect(['index']);
			} else {
				\Yii::error($model->getErrorSummary(true));
				\Yii::$app->session->setFlash("error", \Yii::t("app", "Employee info is not saved"));
			}
		}
		return $this->render('create', [
			'model' => $model
		]);
	}

	public function actionCreateState()
	{
		$model = new State();
		if ( \Yii::$app->request->isPost ) {
			if ( $model->load(\Yii::$app->request->post()) && $model->save() ) {
				$this->redirect(['index']);
			}
		}

		return $this->render('create-state', [
			'model' => $model
		]);
	}
}