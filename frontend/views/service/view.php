<?php

use frontend\models\Client;
use frontend\models\Service;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $model Service
 */
$this->registerCss(".about_info::before { display: none; }");
?>
    <section class="about">
        <div class="container">
            <div class="about_inner">
                <div class="about_content">
					<?php
					echo Html::tag("h2", $model->title, ["class" => "title_without"]);
					echo Html::tag("div", $model->description);
					?>
                </div>
                <div class="about_info">
					<?= Html::img($model->thumbs) ?>
                </div>
            </div>
            <a href="#" class="btn ticket">Заказать услугу</a>
        </div>
    </section>
    <script>
        document.addEventListener("DOMContentLoaded", (e) => {
            $(() => {
                $('.modal-close, .ticket').on("click", (e) => {
                    console.log(e);
                    e.preventDefault();
                    $('.modal').toggleClass('active');
                })
            })
        })
    </script>
    <style>
		.modal {
			display: none;
		}

		.modal.active {
			display: block;
			position: fixed;
			top: 0;
			left: 0;
			width: 100vw;
			height: 100vh;
		}

		.modal.active .modal-body {
			z-index: 999;
			width: 60%;
			min-height: 250px;
			background: rgba(255, 255, 255, 1);
			left: 20%;
			top: calc((100vh - 250px) / 2);
			position: absolute;
			margin: 0 auto;
			border: 3px solid #023250;
			padding: 40px 20px;
			text-align: center;
		}

		.modal.active .modal-body input[type=text] {
			width: 90%;
			padding: 10px;
			margin: 5px 0;
		}

		.modal.active .modal-body button {
			margin: 5px auto;
		}

		.modal.active .modal-close {
			position: absolute;
			top: 10px;
			right: 10px;
			font-size: 32px;
			cursor: pointer;
		}

		.modal.active .modal-close:after {
			clear: both;
		}
    </style>
<?php
echo Html::beginTag("section", ["class" => ["modal"]]);
echo Html::beginTag("div", ["class" => "modal-body"]);
echo Html::tag("div", "X", ["class" => ["modal-close"]]);
?>
    <form action="<?= Url::to(["/ticket/create"]) ?>" method="post">
        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>"
               value="<?= Yii::$app->request->getCsrfToken(); ?>"/>

        <div class="form_content">
            <div class="form_item">
				<?= Html::textInput("Client[name]", "", ["placeholder" => Yii::t("app", "Name")]) ?>
				<?= Html::textInput("Client[phone]", "", ["placeholder" => Yii::t("app", "Phone")]) ?>
				<?= Html::hiddenInput("Ticket[service_id]", $model->id) ?>
            </div>
            <div class="form_item">
                <div class="form_btn">
                    <div class="form_policy">
                        <label for="form_policy">Нажимая кнопку "Отправть" вы даете свое согласие на обработку
                            персональных
                            данных</label>
                    </div>
                    <button type="submit" class="btn blue">
                        отправить
                    </button>
                </div>
            </div>
        </div>
    </form>
<?php
echo Html::endTag("div");
echo Html::tag("div", "", ["class" => ["modal-shadow"]]);
echo Html::endTag("section");