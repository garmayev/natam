<?php

namespace frontend\modules\admin\controllers;

use frontend\models\Staff;

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