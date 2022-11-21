<?php

namespace backend\controllers;

use common\models\search\EmployeeSearch;
use common\models\staff\Employee;
use common\models\staff\State;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Response;

class StaffController extends BaseController
{
	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
				'denyCallback' => function () {
					Url::remember(Url::current());
					return $this->redirect(['user/security/login']);
				}
			],
		];
	}

	public function actionIndex()
	{
        $searchModel = new EmployeeSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
		return $this->render("index", [
			"employeeProvider" => $dataProvider,
            "searchModel" => $searchModel
		]);
	}

    public function actionViewEmployee($id)
    {
        $model = Employee::findOne($id);
        return $this->render("view-employee", [
            "model" => $model
        ]);
    }

    public function actionViewState($id)
    {
        $model = State::findOne($id);
        return $this->render("view-state", [
            "model" => $model
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
		// $token = \Yii::$app->runAction('/cars/login')["SessionId"];
		// $ids = \Yii::$app->runAction('/cars/units', ['token' => $token]);
		if (isset($ids)) {
			foreach ( $ids["Units"] as $id ) {
				$units[$id["UnitId"]] = $id["Name"];
			}
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

    public function actionDeleteEmployee($id) {
        $model = Employee::findOne($id);
        $model->delete();
        return $this->redirect(Url::previous());
    }
}