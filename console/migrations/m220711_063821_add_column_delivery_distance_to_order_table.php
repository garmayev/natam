<?php

use yii\db\Migration;

/**
 * Class m220711_063821_add_column_delivery_distance_to_order_table
 */
class m220711_063821_add_column_delivery_distance_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('order', 'delivery_distance', $this->double());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropColumn('order', 'delivery_distance');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220711_063821_add_column_delivery_distance_to_order_table cannot be reverted.\n";

        return false;
    }
    */
}
