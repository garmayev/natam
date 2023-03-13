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
 * @property int $category_id [int(11)]
 * @property string $article [varchar(255)]
 * @property Category $category
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
            [["title", "price", "value"], "required"],
            [["title", "description", "thumbs", "article"], "string"],
            [["price", "isset", "visible"], "integer"],
            [["price"], "double"],
            [["isset"], "default", "value" => 0],
            [["visible"], "default", "value" => 1],
            [["category_id"], "exist", "targetClass" => Category::class, "targetAttribute" => "id"],
        ];
    }

    public function attributeLabels()
    {
        return [
            "title" => Yii::t("app", "Title"),
            "description" => Yii::t("app", "Description"),
            "price" => Yii::t("app", "Price"),
            "value" => Yii::t("app", "Value"),
            "thumbs" => Yii::t("app", "Picture"),
            "isset" => Yii::t("app", "Isset"),
            "visible" => Yii::t("app", "Visible"),
        ];
    }

    public function fields()
    {
        return [
            "id",
            "title" => function ($model) {
                return "$model->title ($model->value)";
            },
            "description",
            "price",
            "thumbs",
            "value",
            "isset",
            "visible",
            "category" => function ($model) {
                return $model->category;
            }
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            if (isset($this->file->baseName)) {
                $path = "/img/uploads/{$this->file->baseName}.{$this->file->extension}";
//				Yii::error($path);
                $this->file->saveAs(Yii::getAlias("@frontend") . '/web' . $path);
                Yii::error(Yii::getAlias("@frontend") . '/web' . $path);
                $this->thumbs = $path;
            }
        }
        return true;
    }

    /**
     * @return int
     */
    public function getPrice(): int
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

    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }
}