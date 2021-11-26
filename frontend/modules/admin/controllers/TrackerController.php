<?php

namespace frontend\modules\admin\controllers;

use common\modules\admin\models\Speak;

class TrackerController extends BaseController
{
	public function actionIndex()
	{
		$token = Speak::authorize();
		return $this->render("index");
	}
}