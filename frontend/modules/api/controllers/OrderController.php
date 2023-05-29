<?php

namespace frontend\modules\api\controllers;

use common\models\Client;
use common\models\Order;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\Cors;
use yii\rest\ActiveController;

class OrderController extends ActiveController
{
    public $modelClass = Order::class;

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
            'except' => ['options'],
        ];
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);
        unset($actions['create']);
        return $actions;
    }

    public function actionIndex()
    {
        if (Yii::$app->user->can("employee")) {
            return $this->modelClass::find()->where(['<', 'status', Order::STATUS_COMPLETE])->orderBy(['id' => SORT_DESC])->all();
        } else {
            return $this->modelClass::find()->where(['client_id' => Yii::$app->user->identity->client->id])->orderBy(['id' => SORT_DESC])->all();
        }
    }

    public function actionCreate()
    {
        $data = Yii::$app->request->post();
        if (Yii::$app->request->isPost) {
            $client = Client::findOne(["chat_id" => $data["Order"]['chat_id']]);
            $model = new Order();
            if ($client) {
                $data["Order"]["client_id"] = $client->id;
            }
//            if (isset($data["Order"]["chat_id"])) unset($data["Order"]["chat_id"]);
            $model->save(false);
            if ($model->load($data) && $model->save()) {
                return ["ok" => true];
            } else {
                $model->delete();
                return ["ok" => false, "message" => $model->getErrorSummary(true)];
            }
        }
        return ['ok' => false];
    }

    public function actionClone($id)
    {
        $order = Order::findOne($id);
        $newOrder = $order->deepClone($_POST["delivery_date"]);
        return ["ok" => true];
    }
}