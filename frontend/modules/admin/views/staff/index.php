<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use dektrium\user\widgets\Connect;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this yii\web\View
 * @var $model \frontend\models\Staff
 */

$this->title = Yii::t('app', 'Staff');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
    <div class="col-md-3">
		<?= $this->render('//user/settings/_menu') ?>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading">
				<?= Html::encode($this->title) ?>
            </div>
            <div class="panel-body">
				<?php
				$form = ActiveForm::begin([
					'options' => ['class' => 'form-horizontal'],
					'fieldConfig' => [
						'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
						'labelOptions' => ['class' => 'col-lg-3 control-label'],
					],
					'enableAjaxValidation' => true,
					'enableClientValidation' => false,
					'validateOnBlur' => false,
				]);
				echo $form->field($model, 'phone')->textInput();
				echo $form->field($model, 'chat_id')->textInput(["disabled" => true]);
				echo $form->field($model, 'state')->dropDownList([
					\frontend\models\Staff::STATE_MANAGER => "Менеджер",
					\frontend\models\Staff::STATE_STORE => "Кладовщик",
					\frontend\models\Staff::STATE_DRIVER => "Водитель",
                    ], ["disabled" => true]);
                ?>
                <div class="form-group">
                    <div class="col-lg-offset-3 col-lg-9">
			            <?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-block btn-success']) ?>
                        <br>
                    </div>
                </div>
                <?php
				ActiveForm::end();
				?>
            </div>
        </div>
    </div>
</div>
