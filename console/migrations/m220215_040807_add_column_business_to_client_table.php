<?php

use yii\db\Migration;

/**
 * Class m220215_040807_add_column_business_to_client_table
 */
class m220215_040807_add_column_business_to_client_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%client}}", "inn", $this->string());
	    $this->addColumn("{{%client}}", "bik", $this->string());
	    $this->addColumn("{{%client}}", "kpp", $this->string());
	    $this->addColumn("{{%client}}", "ogrn", $this->string());
	    $this->addColumn("{{%client}}", "address", $this->string());
	    $this->addColumn("{{%client}}", "location_id", $this->integer());
		$this->createIndex(
			"idx-client-location_id",
			"{{%client}}",
			"location_id"
		);
		$this->addForeignKey(
			"fk-client-location_id",
			"{{%client}}",
			"location_id",
			"{{%location}}",
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
		$this->dropIndex("idx-client-location_id", "{{%client}}");
		$this->dropForeignKey("fk-client-location_id", "{{%client}}");
		$this->dropColumn("{{%client}}", "inn");
	    $this->dropColumn("{{%client}}", "bik");
	    $this->dropColumn("{{%client}}", "kpp");
	    $this->dropColumn("{{%client}}", "ogrn");
	    $this->dropColumn("{{%client}}", "adress");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220215_040807_add_column_business_to_client_table cannot be reverted.\n";

        return false;
    }
    */
}
