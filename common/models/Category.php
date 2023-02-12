<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $content
 * @property string|null $thumbs
 * @property int $main [int(11)]
 *
 * @property Product[] $products
 * @property int $count
 */
class Category extends \yii\db\ActiveRecord
{
	public $image;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['title', 'thumbs'], 'string', 'max' => 255],
	        [['main'], 'integer'],
	        [['main'], 'default', 'value' => true],
        ];
    }

    public function fields()
    {
	return [
	    'id',
	    'title',
	    'thumbs',
	    'content',
	    'count',
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
            'content' => Yii::t('app', 'Content'),
            'thumbs' => Yii::t('app', 'Thumbs'),
        ];
    }

	public function upload()
	{
		if ( $this->validate() ) {
			if ( isset($this->image) ) {
				$this->image->saveAs(Yii::getAlias('@frontend')."/web/img/uploads/{$this->image->baseName}.{$this->image->extension}");
				$this->thumbs = "/img/uploads/{$this->image->baseName}.{$this->image->extension}";
			}
			return true;
		}
		return false;
	}

    /**
     * Gets query for [[Products]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['category_id' => 'id']);
    }

    public function getCount()
    {
	return count($this->products) > 0;
    }
}
