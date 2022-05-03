<?php

use yii\db\Migration;

/**
 * Class m220222_022522_add_column_blameable_to_updates_table
 */
class m220222_022522_add_column_blameable_to_updates_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("updates", "created_by", $this->integer());
	    $this->addColumn("updates", "updated_by", $this->integer());

		$this->createIndex("idx-updates-created_by", "{{%updates}}", "created_by");
		$this->addForeignKey(
			"fk-updates-created_by",
			"{{%updates}}",
			"created_by",
			"{{%user}}",
			"id");

	    $this->createIndex("idx-updates-updated_by", "{{%updates}}", "updated_by");
	    $this->addForeignKey(
		    "fk-updates-updated_by",
		    "{{%updates}}",
		    "updated_by",
		    "{{%user}}",
		    "id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropIndex("idx-updates-updated_by", "{{%updates}}");
		$this->dropForeignKey("fk-updates-updated_by", "{{%updates}}");

	    $this->dropColumn("{{%updates}}", "updated_by");

	    $this->dropIndex("idx-updates-created_by", "{{%updates}}");
	    $this->dropForeignKey("fk-updates-created_by", "{{%updates}}");

	    $this->dropColumn("{{%updates}}", "created_by");
    }
}