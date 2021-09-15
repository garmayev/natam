<?php

namespace frontend\models;

use dektrium\user\models\User;
use yii\db\ActiveRecord;

/**
 * Модель для новостей
 *
 * @property int $id [int(11)]
 * @property string $title [varchar(128)]
 * @property string $description
 * @property string $thumbs [varchar(255)]
 * @property int $author_id [int(11)]
 * @property int $created_at [int(11)]
 *
 * @property User $author
 */
class Post extends ActiveRecord
{
	public $file;

	public function behaviors()
	{
		return [
			'timestamp' => [
				'class' => 'yii\behaviors\TimestampBehavior',
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
				],
			],
		];
	}

	public static function tableName()
	{
		return "{{%post}}";
	}

	public function rules()
	{
		return [
			[["title", "description"], "required"],
			[["title", "description", "thumbs"], "string"],
			[["author_id"], "integer"],
			[["author_id"], "exist", "targetClass" => User::class, "targetAttribute" => "id"],
			[["author_id"], "default", "value" => \Yii::$app->user->id],
			[["thumbs"], "default", "value" => "/img/news-1.png"]
		];
	}

	public function getAuthor()
	{
		return $this->hasOne(User::class, ["id" => "author_id"]);
	}

	public function upload()
	{
		if ( $this->validate() )
		{
			$path = "/img/uploads/{$this->file->baseName}.{$this->file->extension}";
			if ( file_exists(\Yii::getAlias("@webroot").$this->thumbs) ) {
				unlink(\Yii::getAlias("@webroot").$this->thumbs);
			}
			$this->file->saveAs(\Yii::getAlias("@webroot").$path);
			$this->thumbs = $path;
		} else {
			return false;
		}
		return true;
	}
}