<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 *
 * @property int $id [int(11)]
 * @property string $title [varchar(255)]
 * @property string $description
 * @property string $thumbs [varchar(255)]
 * @property integer $parent_id
 *
 * @property Service $parent
 * @property Service[] $children
 */
class Service extends ActiveRecord
{
	public $file;

	public static function tableName()
	{
		return "{{%service}}";
	}

	public function rules()
	{
		return [
			[["title", "description"], "required"],
			[["title", "description"], "string"]
		];
	}

	public function upload()
	{
		if ($this->validate()) {
			if ( !empty($this->file) ) {
				$this->file->saveAs(\Yii::getAlias("common") . 'img/uploads/' . $this->file->baseName . '.' . $this->file->extension);
				$this->thumbs = "img/uploads/{$this->file->baseName}.{$this->file->extension}";
			}
			return true;
		} else {
			return false;
		}
	}

	public function getParent()
	{
		if ( !is_null($this->parent_id) ) {
			return $this->hasOne(Service::className(), ["id" => "parent_id"]);
		}
		return null;
	}

	public function getChildren()
	{
		return $this->hasMany(Service::className(), ["parent_id" => "id"]);
	}
}