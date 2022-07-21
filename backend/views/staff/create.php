<?php

use common\models\staff\Employee;
use yii\web\View;

/**
 * @var $this View
 * @var $model Employee
 */

$this->title = Yii::t("app", "Create new employee");

echo $this->render("_form", [
	"model" => $model
]);