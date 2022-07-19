<?php

namespace backend\controllers;

use common\models\Category;
use common\models\Product;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\UploadedFile;

class CategoryController extends BaseController
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
		return $this->render('index', [
			"categoryProvider" => new ActiveDataProvider([
				"query" => Category::find()
			])
		]);
	}

	public function actionView($id)
	{
		$model = Category::findOne($id);
		return $this->render("view", [
			"model" => $model,
			"productProvider" => new ActiveDataProvider([
				"query" => Product::find()->where(["category_id" => $model->id])
			])
		]);
	}

	public function actionUpdate($id)
	{
		$model = Category::findOne($id);
		if ( Yii::$app->request->isPost ) {
			$model->image = UploadedFile::getInstance($model, "image");
			if ($model->load(Yii::$app->request->post()) && $model->upload() && $model->save()) {
				Yii::$app->session->setFlash("success", Yii::t("app", "Saved"));
				return $this->redirect(["category/index"]);
			} else {
				Yii::error($model->getErrorSummary(true));
			}
		}
		return $this->render("create", [
			"model" => $model
		]);
	}

	public function actionCreate() {
		$model = new Category();
		if ( Yii::$app->request->isPost ) {
			$model->image = UploadedFile::getInstance($model, "image");
			if ($model->load(Yii::$app->request->post()) && $model->upload() && $model->save()) {
				Yii::$app->session->setFlash("success", Yii::t("app", "Saved"));
				return $this->redirect(["category/index"]);
			} else {
				Yii::error($model->getErrorSummary(true));
			}
		}
		return $this->render("create", [
			"model" => $model
		]);
	}
}