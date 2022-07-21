<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%state}}`.
 */
class m211011_055310_create_state_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%state}}', [
            'id' => $this->primaryKey(),
	        'title' => $this->string(),
			'slug' => $this->string(),
	        'salary' => $this->integer(),
	        'priority' => $this->integer()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%state}}');
    }
}
