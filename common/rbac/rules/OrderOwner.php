<?php

namespace common\rbac\rules;

use common\models\Client;
use common\models\User;
use yii\rbac\Item;

class OrderOwner extends \yii\rbac\Rule
{

	/**
	 * @param string|int $user the user ID.
	 * @param Item $item the role or permission that this rule is associated with
	 * @param array $params parameters passed to ManagerInterface::checkAccess().
	 * @return bool a value indicating whether the rule permits the role or permission it is associated with.
	 */
	public function execute($user, $item, $params)
	{
		$currentClient = Client::findOne(['user_id' => \Yii::$app->user->id]);
		if ( \Yii::$app->user->can('employee') ) {
			\Yii::error('You are has employee role');
			return true;
		}
		if ( !isset($params["order"]) ) {
			\Yii::error('app', 'Missing parameter \$params[\"order\"]');
			return false;
		}
		$result = (isset($currentClient) && $currentClient->id == $params['order']->client_id);
		\Yii::error($result);
		return $result;
	}
}