<?php

use yii\db\Migration;

/**
 * Class m220804_092026_add_column_delivery_city_to_order_table
 */
class m220804_092026_add_column_delivery_city_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("order", "delivery_city", $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("order", "delivery_city");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220804_092026_add_column_delivery_city_to_order_table cannot be reverted.\n";

        return false;
    }
    */
}
