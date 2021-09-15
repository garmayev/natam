<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order}}`.
 */
class m210826_095112_create_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order}}', [
            'id' => $this->primaryKey(),
	        'client_id' => $this->integer(),
	        'address' => $this->string(),
	        'comment' => $this->text(),
	        'status' => $this->integer(),
	        'created_at' => $this->integer(),
        ]);

        $this->createIndex(
        	"idx-order-client_id",
	        "{{%order}}",
	        "client_id"
        );

        $this->addForeignKey(
        	"fk-order-client_id",
	        "{{%order}}",
	        "client_id",
	        "{{%client}}",
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
    	$this->dropForeignKey("fk-order-client_id", "{{%order}}");
    	$this->dropIndex("idx-order-client_id", "{{%order}}");
        $this->dropTable('{{%order}}');
    }
}
