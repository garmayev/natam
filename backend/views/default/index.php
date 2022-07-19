<?php
/**
 * @var $this View
 * @var $cars array
 */

use common\models\Client;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->registerJsVar("carUnits", $cars);

if (Yii::$app->user->can("employee")) {
	$this->registerJsFile("//api-maps.yandex.ru/2.1/?apikey=0bb42c7c-0a9c-4df9-956a-20d4e56e2b6b&lang=ru_RU", ["depends" => \yii\web\JqueryAsset::class]);
	$this->registerJsFile("/admin/js/tracker.js", ["depends" => \yii\web\JqueryAsset::class]);
	$this->title = "";
	?>
    <div class="admin-default-index">
        <?= $this->render('parts/_chart') ?>
        <div id="map" style="height: 800px" class="col-md-12 col-xs-12 col-lg-12"></div>
    </div>
	<?php
} else {
    $clientInfo = Client::findOne(['phone' => Yii::$app->user->identity->username]);
	if (!$clientInfo) {
		$client = new \common\models\Client();
		$form = ActiveForm::begin(["action" => ["client/create"]]);
		echo $form->field($client, "user_id")->hiddenInput(["value" => Yii::$app->user->id])->label(false);
		echo \yii\bootstrap\Tabs::widget([
			"items" => [
				[
					"label" => Yii::t("app", "Basic information"),
					"content" => $this->render("_basic", [
						"client" => $client,
						"form" => $form
					])
				], [
					"label" => Yii::t("app", "Company information"),
					"content" => $this->render("_company", [
						"client" => $client,
						"form" => $form
					])
				]
			]
		]);
		echo \yii\helpers\Html::submitButton("Submit", ["class" => ["btn", "btn-success"]]);
		ActiveForm::end();
	} else {
		echo Html::img("/admin/images/qr/{$clientInfo->phone}.png", ['width' => '300px']);
    }
}