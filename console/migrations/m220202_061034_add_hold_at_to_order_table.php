<?php

use yii\db\Migration;

/**
 * Class m220202_061034_add_holded_at_to_order_table
 */
class m220202_061034_add_hold_at_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%order}}", "hold_at", $this->integer());
	    $this->addColumn("{{%order}}", "hold_time", $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropColumn("{{%order}}", "hold_at");
	    $this->dropColumn("{{%order}}", "hold_time");
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220202_061034_add_holded_at_to_order_table cannot be reverted.\n";

        return false;
    }
    */
}
