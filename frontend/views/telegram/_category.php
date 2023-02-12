<?php

use common\models\Category;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this View
 * @var $model Category
 */
?>
<div class="category" data-key="<?= $model->id ?>">
    <?= Html::img($model->thumbs) ?>
    <?= Html::tag('p', $model->title) ?>
</div>
