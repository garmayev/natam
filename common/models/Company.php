<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $bik
 * @property string|null $kpp
 * @property string|null $ogrn
 * @property string|null $inn
 * @property int|null $boss_id
 *
 * @property Client $boss
 * @property Client[] $workers
 */
class Company extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['boss_id'], 'integer'],
            [['title', 'bik', 'kpp', 'ogrn', 'inn'], 'string', 'max' => 255],
            [['boss_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['boss_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'bik' => Yii::t('app', 'Bik'),
            'kpp' => Yii::t('app', 'Kpp'),
            'ogrn' => Yii::t('app', 'Ogrn'),
            'boss_id' => Yii::t('app', 'Boss ID'),
        ];
    }

    /**
     * Gets query for [[Boss]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBoss()
    {
        return $this->hasOne(Client::className(), ['id' => 'boss_id']);
    }

	public function getWorkers()
	{
		return $this->hasMany(Client::class, ['company_id' => 'id']);
	}

	public function analyze()
	{
	}

	public function join(Client $client)
	{
		$client->company_id = $this->id;
		$client->save();
	}
}
