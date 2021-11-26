<?php

namespace frontend\behaviors;

use yii\base\Model;
use yii\behaviors\AttributeBehavior;

class PhoneNormalizeBehaviors extends AttributeBehavior
{
	public $attributes = null;

	public $value;

	public function events()
	{
		return [
			Model::EVENT_BEFORE_VALIDATE => 'phoneNormalize',
		];
	}

	public function phoneNormalize()
	{
		if ( isset($this->value) ) {

		}
	}
}