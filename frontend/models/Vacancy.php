<?php

namespace frontend\models;

/**
 *
 * @property int $id [int(11)]
 * @property string $title [varchar(255)]
 * @property int $education [int(11)]
 * @property int $experience [int(11)]
 * @property int $status [int(11)]
 */
class Vacancy extends \yii\db\ActiveRecord
{
	const EDUCATION_JUNIOR = 0;
	const EDUCATION_MIDDLE = 1;
	const EDUCATION_SENIOR = 2;

	const EXPERIENCE_SMALL  = 0;
	const EXPERIENCE_MEDIUM = 1;
	const EXPERIENCE_BIG    = 2;

	const STATUS_OPEN = 0;
	const STATUS_CLOSE = 1;

	public static function tableName()
	{
		return "{{%vacancy}}";
	}

	public function rules()
	{
		return [
			[["title"], "required"],
			[["title"], "string"],
			[["education", "experience", "status"], "integer"],
			[["education"], "default", "value" => self::EDUCATION_JUNIOR],
			[["experience"], "default", "value" => self::EXPERIENCE_SMALL],
			[["status"], "default", "value" => self::STATUS_OPEN],
		];
	}

	public function getExperienceLabel($exp = null)
	{
		$experience = [
			self::EXPERIENCE_SMALL => "Без опыта",
			self::EXPERIENCE_MEDIUM => "3-5 лет",
			self::EXPERIENCE_BIG => "Более 5 лет"
		];
		if ( is_null($exp) ) {
			return $experience;
		} else {
			return $experience[$exp];
		}
	}

	public function getEducationLabel($edu = null)
	{
		$education = [
			self::EDUCATION_JUNIOR => "Среднее",
			self::EDUCATION_MIDDLE => "Среднее профессиональное",
			self::EDUCATION_SENIOR => "Высшее"
		];
		if ( is_null($edu) ) {
			return $education;
		} else {
			return $education[$edu];
		}
	}

	public function getStatusLabel($status = null) {
		$statuses = [
			self::STATUS_OPEN => "Открыта",
			self::STATUS_CLOSE => "Закрыта",
		];
		if (is_null($status)) {
			return $statuses;
		} else {
			return $statuses[$status];
		}
	}

	public function attributeLabels()
	{
		return [
			"title" => \Yii::t("app", "Vacancy"),
			"education" => \Yii::t("app", "Education"),
			"experience" => \Yii::t("app", "Experience"),
			"status" => \Yii::t("app", "Status"),
		];
	}
}