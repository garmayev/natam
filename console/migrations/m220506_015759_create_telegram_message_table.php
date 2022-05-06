<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%telegram_message}}`.
 */
class m220506_015759_create_telegram_message_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%telegram_message}}', [
            'id' => $this->primaryKey(),
	        'content' => $this->text(),
	        'chat_id' => $this->integer(),
			'status' => $this->integer(),
			'order_id' => $this->integer(),
	        'created_at' => $this->integer(),
			'created_by' => $this->integer(),
	        'updated_at' => $this->integer(),
	        'updated_by' => $this->integer(),
	        'order_status' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%telegram_message}}');
    }
}
