<?php

use frontend\modules\admin\models\Settings;
use garmayev\staff\models\Employee;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\helpers\Html;

/**
 * @var $this View
 * @var $model Settings
 */

//var_dump($model->getContent());
echo Html::beginForm("/admin/settings/update", "post");
echo Html::beginTag("div", ["class" => "panel panel-default"]);
echo Html::tag("div", "Уведомления для сотрудников", ["class" => "panel-heading"]);

echo Html::beginTag("div", ["class" => "panel-body"]);

echo Html::label("Время для обработки заказа Менеджером", "Settings[notify][limit][]");
echo Html::beginTag("div", ["class" => ["form-group"]]);
echo Html::textInput("Settings[notify][limit][]", $model->getContent()["notify"]["limit"][0], ["class" => "form-control", "placeholder" => "Время дял обработки заказа одного заказа менеджером"]);
echo Html::endTag("div");

echo Html::beginTag("div", ["class" => ["form-group"]]);
echo Html::label("Время для обработки заказа Кладовщиком", "Settings[notify][limit][]");
echo Html::textInput("Settings[notify][limit][]", $model->getContent()["notify"]["limit"][1], ["class" => "form-control", "placeholder" => "Время дял обработки заказа одного заказа кладовщиком"]);
echo Html::endTag("div");

echo Html::beginTag("div", ["class" => ["form-group"]]);
echo Html::label("Время для обработки заказа Водителем", "Settings[notify][limit][]");
echo Html::textInput("Settings[notify][limit][]", $model->getContent()["notify"]["limit"][2], ["class" => "form-control", "placeholder" => "Время дял обработки заказа одного заказа водителем"]);
echo Html::endTag("div");

echo Html::endTag("div");
echo Html::endTag("div");

echo Html::beginTag("div", ["class" => "panel panel-info"]);
echo Html::tag("div", "Настройка трефожноых уведомлений", ["class" => "panel-heading"]);

echo Html::beginTag("div", ["class" => "panel-body"]);

echo Html::beginTag("div", ["class" => ["form-group"]]);
echo Html::label("Время тревоги для первого этапа", "Settings[notify][alert][0][time]");
echo Html::textInput("Settings[notify][alert][0][time]", $model->getContent()["notify"]["alert"][0]["time"], ["class" => "form-control"]);
echo Html::endTag("div");


//if ( isset($model->getContent()["notify"]["alert"][0]["chat_id"]) ) {
echo Html::beginTag("div", ["class" => ["form-group"]]);
echo Html::label("Адресат тревоги", "Settings[notify][alert][0][chat_id]");
echo Html::dropDownList("Settings[notify][alert][0][chat_id]", (isset($model->getContent()["notify"]["alert"][0]["chat_id"])) ? $model->getContent()["notify"]["alert"][0]["chat_id"] : 0, ArrayHelper::map(Employee::find()->where(["<>", "chat_id", "null"])->all(), "chat_id", "user.username"), ["class" => "form-control"]);
echo Html::endTag("div");
//}

echo Html::tag("hr");

echo Html::beginTag("div", ["class" => ["form-group"]]);
echo Html::label("Время тревоги для второго этапа", "Settings[notify][alert][1][time]");
echo Html::textInput("Settings[notify][alert][1][time]", $model->getContent()["notify"]["alert"][1]["time"], ["class" => "form-control"]);
echo Html::endTag("div");

//if (isset($model->getContent()["notify"]["alert"][1]["chat_id"])) {
	echo Html::beginTag("div", ["class" => ["form-group"]]);
	echo Html::label("Адресат тревоги", "Settings[notify][alert][1][chat_id]");
	echo Html::dropDownList("Settings[notify][alert][1][chat_id]", (isset($model->getContent()["notify"]["alert"][1]["chat_id"])) ? $model->getContent()["notify"]["alert"][1]["chat_id"] : 0, ArrayHelper::map(Employee::find()->where(["<>", "chat_id", "null"])->all(), "chat_id", "user.username"), ["class" => "form-control"]);
	echo Html::endTag("div");

//}
echo Html::tag("hr");


echo Html::beginTag("div", ["class" => ["form-group"]]);
echo Html::label("Время тревоги для третьего этапа", "Settings[notify][alert][2][time]");
echo Html::textInput("Settings[notify][alert][2][time]", $model->getContent()["notify"]["alert"][2]["time"], ["class" => "form-control"]);
echo Html::endTag("div");

//if (isset($model->getContent()["notify"]["alert"][2]["chat_id"])) {
	echo Html::beginTag("div", ["class" => ["form-group"]]);
	echo Html::label("Алресат тревоги", "Settings[notify][alert][2][chat_id]");
	echo Html::dropDownList("Settings[notify][alert][2][chat_id]", (isset($model->getContent()["notify"]["alert"][2]["chat_id"])) ? $model->getContent()["notify"]["alert"][2]["chat_id"] : 0, ArrayHelper::map(Employee::find()->where(["<>", "chat_id", "null"])->all(), "chat_id", "user.username"), ["class" => "form-control"]);
	echo Html::endTag("div");
//}

echo Html::endTag("div");
echo Html::endTag("div");

echo Html::submitButton(Yii::t("app", "Save"), ["class" => ["btn", "btn-success"]]);
//echo Html::textInput("Settings[notify][limit][]", $model->getContent()["notify"]["limit"][1])->label("Время дял обработки заказа одного заказа кладовщика");
//echo Html::textInput("Settings[notify][limit][]", $model->getContent()["notify"]["limit"][2])->label("Время дял обработки заказа одного заказа водителя");
echo Html::endForm();

//var_dump(Employee::find()->where(["<>", "chat_id", "null"])->all());