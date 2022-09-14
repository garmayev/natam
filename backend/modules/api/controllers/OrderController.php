<?php

namespace backend\modules\api\controllers;

use backend\modules\api\models\Order;
use yii\data\ActiveDataProvider;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use yii\web\Response;

class OrderController extends ActiveController
{
    public $modelClass = Order::class;

    public function actions()
    {
        return [
            'index' => [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'prepareDataProvider' => [$this, 'getAllData']
            ],
            'view' => [
                'class' => 'yii\rest\ViewAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'create' => [
                'class' => 'yii\rest\CreateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->createScenario,
            ],
            'update' => [
                'class' => 'yii\rest\UpdateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->updateScenario,
            ],
            'delete' => [
                'class' => 'yii\rest\DeleteAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBasicAuth::class,
                HttpBearerAuth::class,
                QueryParamAuth::class,
            ],
        ];
        return $behaviors;
    }

    public function getAllData()
    {
//        $modelClass = $this->modelClass;

//        $timestamp = $_GET["timestamp"];
        $query = (isset($_GET["timestamp"]) && !is_null($_GET["timestamp"])) ? Order::find()->where([">=", "created_at", $_GET["timestamp"]]) : Order::find();
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
    }

    public function actionByStatus($status = null)
    {
        $statusNumber = array_search($status, Order::getStatusList());
        if (is_null($status)) {
            $query = Order::find();
        } else {
            $query = Order::find()->where(['status' => $statusNumber]);
        }
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
    }
}