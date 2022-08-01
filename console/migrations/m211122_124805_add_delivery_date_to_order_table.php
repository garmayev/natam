<?php

use yii\db\Migration;

/**
 * Class m211122_124805_add_delivery_date_to_order_table
 */
class m211122_124805_add_delivery_date_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%order}}', 'delivery_date', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropColumn('{{%order}}', 'delivery_date');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211122_124805_add_delivery_date_to_order_table cannot be reverted.\n";

        return false;
    }
    */
}
