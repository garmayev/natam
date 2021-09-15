<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client}}`.
 */
class m210826_095106_create_client_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%client}}', [
            'id' => $this->primaryKey(),
	        'name' => $this->string(),
	        'phone' => $this->string(),
	        'email' => $this->string(),
	        'company' => $this->string(),
	        'chat_id' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client}}');
    }
}
