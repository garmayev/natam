<?php

namespace frontend\models;

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
			[["price"], "integer"],
			[["price"], "double"],
			[["isset"], "integer"]
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
}