<?php

use yii\web\View;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;


/**
 * @var $this View
 * @var $postProvider ActiveDataProvider
 */

$this->registerCss(".post {
    clear: both;
    width: 80%;
    margin: 0 auto;
    padding: 10px;
}
.img-object {
    max-height: 120px; 
    max-width: 260px;
}
.img {
    float: left; 
    height: 120px; 
    width: 260px;
    margin: 5px 15px; 
    text-align: center;
}
.text {
    text-align: justify;
    font-size: 18px;
}");
?>
<div class="post">
	<div class="container">
		<?php
		echo ListView::widget([
			"dataProvider" => $postProvider,
			"summary" => "",
			"itemView" => "_post"
		]);
		?>
	</div>
</div>
