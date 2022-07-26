<?php

namespace backend\models;

/**
 *
 * @property int $id [int(11)]
 * @property string $name [varchar(255)]
 * @property string $content
 */
class Settings extends \yii\db\ActiveRecord
{
	public function init()
	{
		parent::init();
	}

	/**
	 * Установка атрибута content в закодированном виде (для сохранения)
	 *
	 * @return string
	 */
	public function getContent()
	{
		return json_decode($this->content, true);
	}

	/**
	 * Получение фтрибута content в раскодрованном виде (для работы в системе)
	 *
	 * @param string $content
	 */
	public function setContent($content)
	{
		$this->content = json_encode($content);
	}

	public function getDeliveryCost()
	{
		\Yii::error($this->content);
	}
}