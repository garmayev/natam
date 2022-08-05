<?php

use yii\db\Migration;

/**
 * Class m220716_064716_update_column_chat_id_to_client_table
 */
class m220716_064716_update_column_chat_id_to_client_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('client', 'chat_id', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->alterColumn('client', 'chat_id', $this->integer());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220716_064716_update_column_chat_id_to_client_table cannot be reverted.\n";

        return false;
    }
    */
}
