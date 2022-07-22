<?php

namespace console\controllers;

use common\models\Client;
use common\models\Company;
use dektrium\user\models\Profile;

class HelperController extends \yii\console\Controller
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
				$this->stdout("User {$client->user_id} is deleted\n");
			} else {
				$this->stdout("User {$client->user_id} can`t delete\n");
			}
		}

	}
}