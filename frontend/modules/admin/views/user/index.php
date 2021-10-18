<?php

use dektrium\user\models\User;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Menu;

/**
 * @var $this View
 * @var $userProvider ActiveDataProvider
 * @var $model User
 */

echo Html::a(Yii::t("app", "New User"), ["/admin/user/create"], ["class" => ["btn", "btn-success"]]);

echo GridView::widget([
	"dataProvider" => $userProvider,
	"summary" => "",
	"columns" => [
		"username",
		"email",
		[
			"attribute" => "created_at",
			"content" => function ($model) {
				return Yii::$app->formatter->asDatetime($model->created_at);
			}
		],
		"staff.phone",
		[
			"attribute" => Yii::t("app", "Telegram logged"),
			"content" => function ($model) {
				if ( isset($model->staff->phone) ) {
					if ( isset($model->staff->chat_id) ) {
						return Html::tag("span", "", ["class" => ["glyphicon", "glyphicon-ok"]]);
					} else {
						return Html::a(Yii::t("app", "Send new invite message"), Url::to(["/admin/user/invite", "id" => $model->id]), ["class" => ["btn", "btn-default"]]);
					}
				}
			}
		], [
			"content" => function ($model) {
				$content = Html::button(Html::tag("i", "", ["class" => ["fa", "fa-cog"], "style" => "margin-right: 10px;"]).Html::tag("span", "", ["class" => "caret"]), ["class" => ["btn", "btn-default", "dropdown-toggle"], "data-toggle" => "dropdown", "aria-haspopup" => "true", "aria-expanded" => "false"]);
				$items = [[
					"label" => Yii::t("app", "View"),
					"url" => Url::to(["/admin/user/view", "id" => $model->id]),
				], [
					"label" => Yii::t("app", "Update"),
					"url" => Url::to(["/admin/user/update", "id" => $model->id]),
				], [
					"label" => Yii::t("app", "Delete"),
					"url" => Url::to(["/admin/user/delete", "id" => $model->id]),
				]];
				if ( isset($model->staff->phone) ) {
					array_splice($items, 1, 0, [["label" => Yii::t("app", "Send new invite message"), "url" => Url::to(["/admin/user/invite"])]]);
				}
				$content .= Menu::widget(["items" => $items, "options" => [ "class" => "dropdown-menu"]]);
				return Html::tag("div", $content, ["class" => ["btn-group"]]);
			},
		],
	],
]);

$this->registerCss(".table tbody tr td {line-height: 34px;}");