<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%location}}`.
 */
class m211125_071602_create_location_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%location}}', [
            'id' => $this->primaryKey(),
	        'title' => $this->string(),
	        'latitude' => $this->double(),
	        'longitude' => $this->double(),
        ]);

		$this->addColumn("order", "location_id", $this->integer());

		$this->createIndex(
			"idx-order-location_id",
			"{{%order}}",
			"location_id"
		);

		$this->addForeignKey(
			"fk-order-location_id",
			"{{%order}}",
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
		$this->dropForeignKey("fk-order-location_id", "{{%order}}");
		$this->dropIndex("idx-order-location_id", "{{%order}}");
        $this->dropTable('{{%location}}');
    }
}
