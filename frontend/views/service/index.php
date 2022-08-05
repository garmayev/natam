<?php

use frontend\models\Service;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ListView;

/**
 * @var $this View
 * @var $serviceProvider ActiveDataProvider
 */
$this->registerJsFile('/js/about.js');

?>
<section class="about">
    <div class="container-fluid">
        <div class="services_inner">
			<?php
			/**
			 * @var $service Service
			 */
			echo Html::tag("div",
				Html::tag("h2", "ДОПОЛНИТЕЛЬНЫЕ УСЛУГИ", ["class" => "title"]),
				["style" => "background: url('/img/services-1.png') no-repeat #fff; text-transform: uppercase;", "class" => "services_item"]
			);
			foreach ($serviceProvider->getModels() as $service) {
				echo Html::a(
					Html::tag("h2", $service->title, ["class" => "services_title"]),
					["service/view", "id" => $service->id],
					["style" => "background: url('$service->thumbs') no-repeat; text-transform: uppercase;", "class" => "services_item"]
				);
			}
			?>
        </div>
    </div>
</section>
