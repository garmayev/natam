<?php

namespace backend\controllers;

use common\models\Location;
use common\models\Order;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Response;

class LocationController extends \yii\web\Controller
{
	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
				'denyCallback' => function () {
					Url::remember(Url::current());
					return $this->redirect(['user/security/login']);
				}
			],
		];
	}

	public function beforeAction($action)
	{
		$this->view->title = Yii::t("app", "Address");
		return parent::beforeAction($action);
	}

    public function actionFeatures()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ["type" => "FeatureCollection", "features" => []];
        $used = [];
        foreach (Location::find()->all() as $location) {
            if ( array_search($location->title, $used) === false && count($location->orders) ) {
                $result["features"][] = [
                    "type" => "Feature",
                    "id" => $location->id,
                    "geometry" => [
                        "type" => "Point",
                        "coordinates" => [$location->latitude, $location->longitude],
                    ],
                    "properties" => [
                        "balloonContent" => $location->title,
                    ],
                ];
                $used[] = $location->title;
            }
        }
        return $result;
    }

    public function actionOrders()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Order::find()->where(['id' => Yii::$app->request->post()['id']])->all();
    }

	public function actionView($id)
	{
		$model = Location::findOne($id);
		return $this->render('view', [
			"model" => $model
		]);
	}
}