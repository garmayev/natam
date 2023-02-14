<?php

use common\models\staff\Employee;
use common\models\staff\State;
use common\models\TelegramMessage;
use kartik\date\DatePicker;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this View
 * @var $model Employee
 * @var $units array
 */


$form = ActiveForm::begin();
echo $form->field($model, 'name');
echo $form->field($model, 'family');
echo $form->field($model, 'phone');
echo $form->field($model, 'birth')->widget(DatePicker::class, [
    'pluginOptions' => [
        'format' => 'dd/mm/yyyy',
        'todayHighlight' => true
    ]
]);
echo $form->field($model, 'state_id')->dropDownList(ArrayHelper::map(State::find()->all(), 'id', 'title'))->label(Yii::t("app", "State"));
if ($model->state_id === 3) {
    echo $form->field($model, 'car')->dropDownList($units);
    echo $form->field($model, 'engine')->textInput();
}
if (!$model->isNewRecord && $model->state_id === 0) {
    echo $form->field($model, 'level')->dropDownList([
        TelegramMessage::LEVEL_INFO => Yii::t('app', 'Alert with info level'),
        TelegramMessage::LEVEL_WARNING => Yii::t('app', 'Alert with warning level')
    ]);
}

echo Html::submitButton(Yii::t('app', 'Save'), ['class' => ['btn', 'btn-success']]);
ActiveForm::end();