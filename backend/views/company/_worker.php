<?php
/**
 * @var $this View
 * @var $model Company
 */

use common\models\Client;
use common\models\Company;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

$data = Client::find()->where(['company_id' => null])->all();
$clients = [];
foreach ($data as $item) $clients[$item->id] = "{$item->name} ({$item->phone})";

?>
<!-- Button trigger modal -->
<p>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
        Добавить сотрудника
    </button>
</p>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Добавить сотрудника</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="worker">
                <div class="modal-body">
                    <div class="form-group">
                    <?= Html::dropDownList('Client[id]', null, $clients, ['class' => 'form-control', 'id' => 'client-id']) ?>
                    </div>
                    <?= Html::hiddenInput('Client[company_id]', $model->id, ['id' => 'client-company_id']) ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
