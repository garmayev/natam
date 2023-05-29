<?php

namespace frontend\modules\api\controllers;

use common\models\Client;
use common\models\Order;
use common\models\OrderProduct;
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
            return $this->modelClass::find()->where(['<', 'status', Order::STATUS_COMPLETE])->all();
        } else {
            return $this->modelClass::find()->where(['client_id' => Yii::$app->user->identity->client->id])->all();
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
        $newOrder = new Order();
        $newOrder->attributes = $order->attributes;
        $newOrder->status = Order::STATUS_NEW;
        $newOrder->delivery_date = Yii::$app->formatter->asTimestamp($_POST["delivery_date"]);
        if ($newOrder->save()) {
            foreach ($newOrder->orderProducts as $orderProduct) {
                $op = new OrderProduct();
                $op->attributes = $orderProduct->attributes;
                $op->order_id = $newOrder->id;
                $op->save();
            }
            return ["ok" => true];
        }
        return ["ok" => false];
    }
}