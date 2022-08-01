<?php

use common\models\Client;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Company */
/* @var $form yii\widgets\ActiveForm */

$this->registerJs(<<<JS
$('#exampleModal .btn-secondary').on('click', (e) => {
    e.preventDefault();
    $('#exampleModal').modal('hide');
})
$('#exampleModal .btn-primary').on('click', (e) => {
    e.preventDefault();
    console.log($('#worker').serialize());
    $.post('/admin/client/update?id='+$('#client-id').val(), {'Client[company_id]': $('#client-company_id').val()}, (response) => {
        $("#exampleModal").modal("hide");
        window.location.reload();
    })
    // $.ajax({
    //     url: '/admin/client/update?id='+$('#client-id').val(),
    //     method: 'POST',
    //     data: $('#worker').serialize(),
    //     success: (response) => {
    //         console.log(response);
    //         $('#exampleModal').modal('hide');
    //     }
    // })
}) 
JS, View::POS_LOAD);

?>

<div class="company-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bik')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'kpp')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ogrn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'boss_id')->dropDownList(ArrayHelper::map(Client::find()->all(), 'id', 'name')) ?>

    <?php
    if ( !$model->isNewRecord ) {
        echo $this->render('_worker', [
                'model' => $model
        ]);
        ?>
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <td><?= Yii::t('app', 'Fullname') ?></td>
                <td><?= Yii::t('app', 'Staff`s Phone') ?></td>
                <td><?= Yii::t('app', 'email') ?></td>
            </tr>
            </thead>
            <tbody>

    <?php
        foreach ($model->workers as $worker) {
            echo Html::beginTag("tr");
            echo Html::tag("td", Html::a($worker->name, ["client/view", 'id' => $worker->id]));
            echo Html::tag("td", Html::a($worker->phone, "tel:+$worker->phone"));
            echo Html::tag("td", $worker->email);
            echo Html::endTag("tr");
        }
        ?>
            </tbody>
        </table>
    <?php
    }
    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
