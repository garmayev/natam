<?php

use common\models\staff\Employee;
use yii\web\View;

/**
 * @var $this View
 * @var $model Employee
 * @var $units array
 */

$this->title = Yii::t("app", "Update employee {name}", ["name" => $model->fullname]);

echo $this->render("_form", [
	"model" => $model,
	"units" => $units,
]);