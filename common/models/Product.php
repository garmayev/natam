<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 *
 * @property int $id [int(11)]
 * @property string $title [varchar(255)]
 * @property string $description
 * @property int $price [int(11)]
 * @property float $value [float]
 * @property string $thumbs [varchar(255)]
 * @property int $isset [int(11)]
 * @property int $visible [int(11)]
 *
 * @property-read int $uniqueId
 * @property-read string $label
 */
class Product extends ActiveRecord
{
	public $file;

	public static function tableName()
	{
		return "{{%product}}";
	}

	public function rules()
	{
		return [
			[["title", "description", "price", "value"], "required"],
			[["title", "description", "thumbs"], "string"],
			[["price", "isset", "visible"], "integer"],
			[["price"], "double"],
			[["isset"], "default", "value" => 0],
			[["visible"], "default", "value" => 1],
		];
	}

	public function attributeLabels()
	{
		return [
			"title" => Yii::t("natam", "Title"),
			"description" => Yii::t("natam", "Description"),
			"price" => Yii::t("natam", "Price"),
			"value" => Yii::t("natam", "Value"),
			"thumbs" => Yii::t("natam", "Picture"),
			"isset" => Yii::t("natam", "Isset"),
			"visible" => Yii::t("natam", "Visible"),
		];
	}

	public function upload()
	{
		if ( $this->validate() ) {
			if ( isset($this->file->baseName) ) {
				$path = "/img/uploads/{$this->file->baseName}.{$this->file->extension}";
				Yii::error($path);
				$this->file->saveAs(Yii::getAlias("@webroot") . $path);
				Yii::error(Yii::getAlias("@webroot") . $path);
				$this->thumbs = $path;
			}
		}
		return true;
	}

	/**
	 * @return int
	 */
	public function getPrice() : int
	{
		return $this->price;
	}

	public function getLabel()
	{
		return $this->title;
	}

	public function getUniqueId()
	{
		return $this->id;
	}
}