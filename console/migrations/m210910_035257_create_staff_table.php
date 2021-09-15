<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%staff}}`.
 */
class m210910_035257_create_staff_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%staff}}', [
            'id' => $this->primaryKey(),
	        'user_id' => $this->integer(),
	        'state' => $this->integer(),
	        'phone' => $this->string(),
	        'chat_id' => $this->integer(),
	        'message_id' => $this->integer(),
	        'message_timestamp' => $this->integer(),
        ]);

		$this->createIndex(
			'idx-staff-user_id',
			'staff',
			'user_id'
		);

		$this->addForeignKey(
			'fk-staff-user_id',
			'staff',
			'user_id',
			'user',
			'id',
			'CASCADE',
			'CASCADE'
		);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropForeignKey('fk-staff-user_id', 'staff');
		$this->dropIndex('idx-staff-user_id', 'staff');
        $this->dropTable('{{%staff}}');
    }
}
