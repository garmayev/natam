<Items>
	<?php

	use yii\data\ActiveDataProvider;
	use yii\widgets\ListView;

	/**
	 * @var $dataProvider ActiveDataProvider
	 */

	echo ListView::widget([
		'itemView' => '_item',
		'dataProvider' => $dataProvider
	])
	?>
</Items>