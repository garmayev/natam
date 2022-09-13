<?php
/**
 * @var $this View
 * @var $cars array
 */

use common\models\Client;
use common\models\Order;
use Da\QrCode\QrCode;
use dektrium\user\helpers\Password;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = Yii::t("app", "Admin Panel");

if (Yii::$app->user->can("employee")) {
	$this->registerJsVar("carUnits", $cars);

	$this->registerJsFile("//api-maps.yandex.ru/2.1/?apikey=0bb42c7c-0a9c-4df9-956a-20d4e56e2b6b&lang=ru_RU", ["depends" => \yii\web\JqueryAsset::class]);
	$this->registerJsFile("/admin/js/tracker.js", ["depends" => \yii\web\JqueryAsset::class]);
	?>
    <div class="admin-default-index">
        <div id="map" class="col-md-12 col-xs-12 col-lg-12"></div>
    </div>
	<?php
} elseif (Yii::$app->user->can('person')) {
	$client = Client::findOne(['user_id' => Yii::$app->user->id]);
    echo Html::beginTag("p");
	if (empty($client->chat_id)) {
		Modal::begin([
			'header' => 'QR Code for Telegram Authorization',
			'toggleButton' => ['label' => Yii::t('app', 'Show QR for Telegram'), 'class' => ['btn', 'btn-primary']],
		]);
		if (!file_exists(\Yii::getAlias('@webroot') . "/images/qr/{$client->phone}.png")) {
			$qrCode = (new QrCode("https://t.me/" . \Yii::$app->telegram->botUsername . "?start={$client->phone}"))
				->setSize(300)
				->setMargin(10);
			$qrCode->writeFile(\Yii::getAlias('@webroot') . "/images/qr/{$client->phone}.png");
		}
		echo Html::beginTag("div", ["style" => "text-align: center"]);
		echo Html::img("/admin/images/qr/{$client->phone}.png", ['style' => 'margin: 0 auto;']);
		echo Html::endTag("div");
		Modal::end();
	}
    echo Html::endTag("p");
}