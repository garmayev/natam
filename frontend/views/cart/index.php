<?php


/**
 * @var $this \yii\web\View
 */

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Client;
use common\models\Order;
use common\models\Product;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

$cart = Yii::$app->cart;
$items = $cart->getItems();

