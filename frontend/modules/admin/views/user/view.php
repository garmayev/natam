<?php

use dektrium\user\models\User;
use common\models\Staff;
use yii\web\View;
use yii\helpers\Html;

/**
 * @var $this View
 * @var $model User
 */

echo Html::a(Yii::t("app", "Update"), ["/admin/user/update", "id" => $model->id], ["class" => ["btn", "btn-success"]]);

echo Html::beginTag("div", ["class" => "row"]);
/**
 * User Info
 */
echo Html::beginTag("div", ["class" => ["col-lg-6", "col-md-6", "col-xs-12"]]);
echo Html::beginTag("table", ["class" => ["table", "table-striped"]]);

echo Html::beginTag("tr");
echo Html::tag("td", Yii::t("user", "Username"));
echo Html::tag("td", $model->username);
echo Html::endTag("tr");

echo Html::beginTag("tr");
echo Html::tag("td", Yii::t("user", "Email"));
echo Html::tag("td", $model->email);
echo Html::endTag("tr");

echo Html::beginTag("tr");
echo Html::tag("td", Yii::t("user", "Registration time"));
echo Html::tag("td", Yii::$app->formatter->asDatetime($model->created_at));
echo Html::endTag("tr");

echo Html::beginTag("tr");
echo Html::tag("td", Yii::t("user", "Last login"));
echo Html::tag("td", Yii::$app->formatter->asDatetime($model->last_login_at));
echo Html::endTag("tr");

echo Html::endTag("table");
echo Html::endTag("div");

/**
 * Profile info
 */
echo Html::beginTag("div", ["class" => ["col-lg-6", "col-md-6", "col-xs-12"]]);
echo Html::beginTag("table", ["class" => ["table", "table-striped"]]);

echo Html::beginTag("tr");
echo Html::tag("td", Yii::t("user", "Name"));
echo Html::tag("td", $model->profile->name);
echo Html::endTag("tr");

echo Html::beginTag("tr");
echo Html::tag("td", Yii::t("user", "Gravatar email"));
echo Html::tag("td", $model->profile->gravatar_email);
echo Html::endTag("tr");

echo Html::beginTag("tr");
echo Html::tag("td", Yii::t("user", "Bio"));
echo Html::tag("td", $model->profile->bio);
echo Html::endTag("tr");

echo Html::beginTag("tr");
echo Html::tag("td", Yii::t("user", "Time zone"));
echo Html::tag("td", $model->profile->timezone);
echo Html::endTag("tr");

echo Html::endTag("table");
echo Html::endTag("div");

/**
 * Staff info
 */

$staff = \frontend\models\Staff::find()->where(["user_id" => $model->id])->one();

echo Html::beginTag("div", ["class" => ["col-lg-6", "col-md-6", "col-xs-12"]]);
echo Html::beginTag("table", ["class" => ["table", "table-striped"]]);

echo Html::beginTag("tr");
echo Html::tag("td", Yii::t("app", "Phone"));
echo Html::tag("td", $staff->phone);
echo Html::endTag("tr");

echo Html::beginTag("tr");
echo Html::tag("td", Yii::t("app", "State"));
echo Html::tag("td", $staff->getStateLabel());
echo Html::endTag("tr");

echo Html::beginTag("tr");
echo Html::tag("td", Yii::t("app", "Telegram logged"));
echo Html::tag("td", Html::checkbox("telegram-logged", isset($staff->chat_id), ["class" => "disabled", "disabled" => "disabled"]));
echo Html::endTag("tr");

echo Html::endTag("table");
echo Html::submitButton(Yii::t("app", "Send new invite message"), ["class" => ["btn", "btn-info"], "data-key" => $model->id]);
echo Html::endTag("div");
echo Html::endTag("div");

$this->registerJs("$(() => {
	$('.btn-info').on('click', () => {
		
	})
})");