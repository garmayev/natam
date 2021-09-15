<?php

use frontend\models\Post;
use yii\helpers\Html;

/**
 * @var $model Post
 */
?>

<div class="media">
	<div class="media-left">
		<?= Html::img($model->thumbs, ["class" => "media-object", "alt" => $model->title, "style" => "margin: 15px 0;", "width" => 350]) ?>
	</div>
	<div class="media-body">
        <?php
            echo Html::tag("h3", $model->title, ["class" => "media-heading"]);
            echo Html::beginTag("div", ["class" => "content"]);
            if (strlen($model->description) > 666) {
	            echo str_split($model->description, 666)[0]."...";
                echo Html::tag("hr");
                echo Html::a("Read more...", ["post/view", "id" => $model->id], ["class" => ["btn", "btn-success"]]);
            } else {
                echo $model->description;
                echo Html::tag("hr");
            }
            echo Html::a(Yii::t("app", "Update"), ["post/update", "id" => $model->id], ["class" => ["btn", "btn-primary"], "style" => "margin: 0 15px;"]);
            echo Html::a(Yii::t("app", "Delete"), ["post/delete", "id" => $model->id], ["class" => ["btn", "btn-danger"]]);
            echo Html::endTag("div");
        ?>
		<div class="media-bottom">
            <?php
            ?>
		</div>
	</div>
</div>
