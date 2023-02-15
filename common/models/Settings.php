<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 *
 * @property int $id [int(11)]
 * @property string $name [varchar(255)]
 * @property string $content
 */
class Settings extends ActiveRecord
{
    public static function tableName()
    {
        return "settings";
    }

    public static function getInterval($index)
    {
        $value = json_decode(Settings::findOne(1)->content, true);
        return (isset($value["notify"]["alert"][$index]["time"])) ? $value["notify"]["alert"][$index]["time"] : 0;
    }

    public static function getDeliveryCost()
    {
        $value = json_decode(Settings::findOne(1)->content, true);
        if (isset($value["delivery_cost"])) {
            return $value["delivery_cost"];
        } else {
            return 0;
        }
    }

    public function getContent()
    {
        return json_decode($this->content, true);
    }

    public function setContent($value)
    {
        $this->content = json_encode($value);
    }
}