<?php

use common\models\Client;
use common\models\Ticket;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


/**
 * @var $this View
 * @var $model Ticket
 */
$this->title = Yii::t("app", "Edit ticket #{n}", ["n" => $model->id]);
$form = ActiveForm::begin();
?>

    <div class="panel">
        <div class="panel-heading panel-default">
			<?= Yii::t("app", "Information about Client") ?>
        </div>
		<?php
		if (is_null($client_id = Yii::$app->session->get("client_id"))) {
			echo Html::beginTag("div", ["class" => ["panel-body"]]);
			if (is_null($model->client)) {
				$client = new Client();
			} else {
				$client = Client::findOne($model->client_id);
			}
		} else {
			$client = Client::findOne($client_id);
		}
		echo Html::tag("label", $client->getAttributeLabel("name"), ["class" => "control-label", "for" => "client-name"]);
		echo Html::tag("p", Html::activeTextInput($client, "name", ["class" => "form-control", "placeholder" => $client->getAttributeLabel("name")]));
		echo Html::tag("label", $client->getAttributeLabel("phone"), ["class" => "control-label", "for" => "client-phone"]);
		echo Html::tag("p", Html::activeTextInput($client, "phone", ["class" => "form-control", "placeholder" => $client->getAttributeLabel("phone")]));
		echo Html::endTag("div");
		?>
    </div>

    <div class="panel">
        <div class="panel-heading panel-default">
			<?= Yii::t("app", "Information about Ticket") ?>
        </div>
        <div class="panel-body">
            <p>Статус
                запроса: <?= Html::tag("div", Html::activeDropDownList($model, "status", $model->getStatus(), ["class" => "form-control"]), ["class" => "form-group"]) ?></p>
			<?= Html::a(Yii::t("app", "Convert to Order"), ["ticket/convert", "client_id" => $model->client->id, "id" => $model->id], ["class" => ["btn", "btn-success"]]) ?>
        </div>
    </div>
<?php
echo Html::submitButton(Yii::t("app", "Save"), ["class" => ["btn", "btn-success"]]);
echo Html::a(Yii::t("app", "Cancel"), ["/admin/ticket/index"], ["class" => ["btn", "btn-danger"]]);
ActiveForm::end();
$this->registerCss(".btn {margin-right: 15px;}");