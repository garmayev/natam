<?php

use yii\db\Migration;

/**
 * Class m220214_070310_add_user_id_to_client_table
 */
class m220214_070310_add_user_id_to_client_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%client}}", "user_id", $this->integer());
		$this->createIndex(
			"idx-client-user_id",
			"{{%client}}",
			"user_id"
		);
		$this->addForeignKey(
			"fk-client-user_id",
			"{{%client}}",
			"user_id",
			"{{%user}}",
			"id",
			"CASCADE",
			"CASCADE"
		);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220214_070310_add_user_id_to_client_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220214_070310_add_user_id_to_client_table cannot be reverted.\n";

        return false;
    }
    */
}
