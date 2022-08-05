<?php

use common\models\Client;
use common\models\staff\Employee;
use yii\helpers\Html;
use yii\helpers\Json;
use yii2mod\rbac\RbacAsset;

RbacAsset::register($this);

/* @var $this yii\web\View */
/* @var $model \yii2mod\rbac\models\AssignmentModel */
/* @var $usernameField string */

if ( !empty($model->user->profile->name) ) {
	$userName = $model->user->profile->name;
} elseif ( $client = Client::findOne(['user_id' => $model->userId]) ) {
    $userName = $client->name;
} elseif ( $employee = Employee::findOne(['user_id' => $model->userId]) ) {
	$userName = $employee->getFullname();
} else {
    $userName = $model->user->username;
}
$this->title = Yii::t('yii2mod.rbac', 'Assignment : {0}', $userName);
$this->params['breadcrumbs'][] = ['label' => Yii::t('yii2mod.rbac', 'Assignments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $userName;
$this->render('/layouts/_sidebar');
?>
<div class="assignment-index">

    <?php echo $this->render('../_dualListBox', [
        'opts' => Json::htmlEncode([
            'items' => $model->getItems(),
        ]),
        'assignUrl' => ['assign', 'id' => $model->userId],
        'removeUrl' => ['remove', 'id' => $model->userId],
    ]); ?>

</div>
