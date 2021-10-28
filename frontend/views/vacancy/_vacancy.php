<?php

use frontend\models\Vacancy;
use yii\web\View;
use yii\helpers\Html;

/**
 * @var $this View
 * @var $model Vacancy
 */

//echo Html::a(Html::tag("div",
//		Html::tag("p", $model->title, ["class" => "news_title"]).
//		Html::tag("div", Yii::t("app", "Education").": ".$model->getEducationLabel($model->education)).
//		Html::tag("div", Yii::t("app", "Experience").": ".$model->getEducationLabel($model->experience)),
//		["class" => "news_content"]
//	), ["#"]);

//echo Html::tag("h2", $model->title);
//echo Html::tag("p", "Образование: ".(is_null($model->education) ? "Не требуется" : $model->getEducationLabel($model->education)));
//echo Html::tag("p", "Опыт работы: ".(is_null($model->experience) ? "Не требуется" : $model->getExperienceLabel($model->experience)));
?>
<div class="news_content">
	<p class="news_title"><?= $model->title ?></p>
	<?php
		if (!empty($model->thumbs)) {
	?>
	<div class="thumbnail">
		<img src="<?= $model->thumbs ?>">
	</div>
	<?php
		}
	?>
	<div>
		<div><?= Yii::t("app", "Education").": ".$model->getEducationLabel($model->education) ?></div>
		<div><?= Yii::t("app", "Experience").": ".$model->getEducationLabel($model->experience) ?></div>
	</div>
</div>
