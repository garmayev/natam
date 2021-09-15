<?php

use frontend\models\Post;
use yii\web\View;
use yii\helpers\Html;

/**
 * @var $this View
 * @var $model Post
 */
$this->registerJs("$('.description > p').addClass('text')")
?>
<section class="about">
	<div class="container">
		<div class="about_inner">
			<div class="about_content">
				<h2 class="title without"><?= $model->title ?></h2>
                <div class="description">
	                <?= $model->description ?>
                </div>
			</div>
			<div class="about_info">
				<div class="about_slider slick-initialized slick-slider">
					<div class="slick-list draggable">
						<div class="slick-track">
							<div class="about_item slick-slide slick-current slick-active" data-slick-index="-1" aria-hidden="true" tabindex="-1" style="width: 608px;">
								<?= Html::img($model->thumbs, ["alt" => $model->title]) ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>