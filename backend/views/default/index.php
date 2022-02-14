<?php
/**
 * @var $this View
 */

use yii\web\View;
use yii\widgets\ActiveForm;


if (Yii::$app->user->can("employee")) {
	$this->registerJsFile("//api-maps.yandex.ru/2.1/?apikey=0bb42c7c-0a9c-4df9-956a-20d4e56e2b6b&lang=ru_RU", ["depends" => \yii\web\JqueryAsset::class]);
//$this->registerCssFile("http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.css", ["depends" => \yii\web\JqueryAsset::class]);
//$this->registerJsFile("http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.js", ["depends" => \yii\web\JqueryAsset::class]);
	$this->registerJsFile("/js/tracker.js", ["depends" => \yii\web\JqueryAsset::class]);
	$this->title = "";
	?>

    <div class="admin-default-index">
        <div id="map" style="height: 800px"></div>
    </div>
	<?php
} else {
	if (!$clientInfo = Yii::$app->user->identity->client) {
		$client = new \common\models\Client();
		$form = ActiveForm::begin();
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
        ActiveForm::end();
    }
}