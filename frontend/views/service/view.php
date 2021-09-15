<?php

use frontend\models\Service;
use yii\web\View;
use yii\helpers\Html;

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
	</div>
</section>
<?php
echo Html::beginTag("section", ["class" => "about"]);
echo Html::beginTag("div", ["class" => "container"]);


echo Html::endTag("div");
echo Html::endTag("section");