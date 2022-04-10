<?php

namespace backend\controllers;

use common\models\Staff;

class StaffController extends BaseController
{
	public function actionIndex() {
		$staff = Staff::find()->where(["user_id" => \Yii::$app->user->id])->one();
		if ( \Yii::$app->request->isPost ) {
			if ( $staff->load(\Yii::$app->request->post()) && $staff->save() ) {
				\Yii::$app->session->setFlash("success", "Information is saved!");
			}
		}
		return $this->render("index", [
			"model" => $staff
		]);
	}
}