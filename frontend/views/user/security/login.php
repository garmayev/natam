<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var dektrium\user\models\User $user
 */

$this->title = Yii::t('user', 'Sign in');
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="about">
    <div class="container">
        <div class="row login">
            <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            <div class="form-inner">
				<?php
				$form = ActiveForm::begin([
					'id' => 'login-form',
					"options" => [
					    'class' => 'form_block'
                    ]
				]);
				echo Html::beginTag("div", ["class" => "form-content"]);
				echo Html::beginTag("div", ["class" => "form-item"]);
				echo Html::activeTextInput($model, 'login', ["placeholder" => $model->getAttributeLabel("login")]);
				echo Html::activePasswordInput($model, 'password', ["placeholder" => $model->getAttributeLabel("password")]);
                echo Html::submitButton(Yii::t('user', 'Sign in'), ['class' => 'btn btn-success btn-block']);
                echo Html::endTag("div");
                echo Html::endTag("div");
				?>
				<?php ActiveForm::end(); ?>
            </div>
            <p class="text-center">
		        <?= Html::a(Yii::t('user', "Don't have an account? Sign up!"), ['/user/registration/register']) ?>
            </p>
        </div>
    </div>
</section>