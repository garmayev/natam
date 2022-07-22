<?php

use yii\db\Migration;

/**
 * Class m220527_041531_add_column_created_at_to_ticket_table
 */
class m220527_041531_add_column_created_at_to_ticket_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%ticket}}', 'created_at', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropColumn('{{%ticket}}', 'created_at');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220527_041531_add_column_created_at_to_ticket_table cannot be reverted.\n";

        return false;
    }
    */
}
