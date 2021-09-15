<?php

use frontend\models\Ticket;
use yii\web\View;
use yii\helpers\Html;


/**
 * @var $this View
 * @var $model Ticket
 */

echo Html::a(Yii::t("app", "Update"), ["ticket/update", "id" => $model->id], ["class" => ["btn", "btn-success"]]);
echo Html::a(Yii::t("app", "Cancel"), ["ticket/index"], ["class" => ["btn", "btn-danger"]]);
?>
<table class="table table-striped">
	<tbody>
		<tr>
			<td><?= Html::tag("b", Yii::t("app", "Customer`s name")) ?></td>
			<td><?= $model->client->name ?></td>
		</tr>
		<tr>
			<td><?= Html::tag("b", Yii::t("app", "Customer`s phone")) ?></td>
			<td><?= $model->client->phone ?></td>
		</tr>
		<tr>
			<td><?= Html::tag("b", Yii::t("app", "Status")) ?></td>
			<td><?= $model->getStatus($model->status) ?></td>
		</tr>
	</tbody>
</table>