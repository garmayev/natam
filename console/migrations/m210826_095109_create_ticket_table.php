<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ticket}}`.
 */
class m210826_095109_create_ticket_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ticket}}', [
            'id' => $this->primaryKey(),
	        'client_id' => $this->integer(),
	        'status' => $this->integer(),
	        'comment' => $this->text(),
        ]);

        $this->createIndex(
        	"idx-ticket-client_id",
	        "{{%ticket}}",
	        "client_id"
        );

	    $this->addForeignKey(
	    	"fk-ticket-client_id",
		    "{{%ticket}}",
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
    	$this->dropForeignKey("fk-ticket-client_id", "{{%ticket}}");
    	$this->dropIndex("idx-ticket-client_id", "{{%ticket}}");
        $this->dropTable('{{%ticket}}');
    }
}
