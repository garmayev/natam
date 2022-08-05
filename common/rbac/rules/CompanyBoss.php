<?php

namespace common\rbac\rules;

use common\models\Client;
use common\models\User;
use yii\rbac\Item;

class CompanyBoss extends \yii\rbac\Rule
{

	/**
	 * @param string|int $user the user ID.
	 * @param Item $item the role or permission that this rule is associated with
	 * @param array $params parameters passed to ManagerInterface::checkAccess().
	 * @return bool a value indicating whether the rule permits the role or permission it is associated with.
	 */
	public function execute($user, $item, $params)
	{
		$client = Client::findOne(['user_id' => $user]);
		return ($item->name === 'employee') && $client->id === $params["company"]->boss_id;
	}
}