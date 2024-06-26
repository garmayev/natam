<?php

namespace console\controllers;

use common\models\Client;
use common\models\Company;
use common\models\Order;
use common\models\User;
use dektrium\user\models\Profile;
use dektrium\user\models\Token;
use Yii;
use yii\console\Controller;

class HelperController extends Controller
{
    public function actionParseCompany()
    {
        $clients = Client::find()->all();
        foreach ($clients as $client) {
            $companyName = $client->company;
            if (!empty($companyName)) {
                $company = Company::findOne(['title' => $companyName]);
                if (empty($company)) {
                    $company = new Company(['title' => $companyName]);
                    $company->boss_id = $client->id;
                    $company->save();
                    $client->company_id = $company->id;
                    $client->save();
                    $this->stdout("Company $companyName is saved\n");
//					$company->analyze()->save();
                } else {
                    $client->company_id = $company->id;
                    $client->save();
                    $this->stdout("Company $companyName already exists\n");
                }
            } else {
                $this->stdout("$client->name is not set company name");
            }
        }
    }

    public function actionClearUser()
    {
        $deleted = [
            "Henrylar",
            "ThomastAcle",
            "Matthewthand",
            "RobertKet",
            "NikkelonDep",
            "Jorgejor",
            "Crytolarlar",
            "Crytolar"
        ];
        $clients = Profile::find()->where(['in', 'name', $deleted])->all();
        foreach ($clients as $client) {
            if ($client->user->delete()) {
                $client->delete();
                $this->stdout("User {$client->user_id} is deleted\n");
            } else {
                $this->stdout("User {$client->user_id} can`t delete\n");
            }
        }

    }

    public function actionTest($order_id)
    {
        $order = Order::findOne($order_id);
        $order->deepClone();
    }

    public function actionPhones()
    {
        foreach (Client::find()->all() as $client) {
            $this->stdout("| $client->name\t| $client->phone\t| https://t.me/natam_trade_bot?start=$client->phone |\n");
        }
    }

    public function actionXml($id = null)
    {
        if (is_null($id)) {
            $orders = Order::find()->all();
        } else {
            $orders = Order::find()->where(["id" => $id])->all();
        }
        foreach ($orders as $order) {
            $order->createFile("/data/www/natam/frontend/web/xml/");
        }
//        $this->stdout(count($orders));
    }

    public function actionGenerateToken()
    {
        $users = User::find()->all();
        foreach ($users as $user) {
            $token = Token::findOne($user->id);
            if (empty($token)) {
                $this->stdout($user->id . "\n");
                $token = Yii::createObject(['class' => Token::className(), 'type' => Token::TYPE_CONFIRMATION, 'code' => Yii::$app->security->generateRandomString()]);
                $token->link('user', $user);
            }
        }
    }

    public function actionCheckStatus($id = null) {
		$order = Order::findOne($id);
		echo $this->stdout($order->statusName . "\n");
    }
}