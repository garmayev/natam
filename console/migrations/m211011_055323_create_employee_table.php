<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%employee}}`.
 */
class m211011_055323_create_employee_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%employee}}', [
            'id' => $this->primaryKey(),
	        'name' => $this->string(),
	        'family' => $this->string(),
			'phone' => $this->string(),
	        'birthday' => $this->integer(),
	        'user_id' => $this->integer(),
			'state_id' => $this->integer(),
	        'chat_id' => $this->integer(),
        ]);

		$this->createIndex(
			'idx-employee-user_id',
			'employee',
			'user_id'
		);

		$this->addForeignKey(
			'fk-employee-user_id',
			'employee',
			'user_id',
			'user',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->createIndex(
			"idx-employee-state_id",
			"employee",
			"state_id"
		);

		$this->addForeignKey(
			"fk-employee-state_id",
			"employee",
			"state_id",
			"state",
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
		$this->dropForeignKey('fk-employee-state_id', 'employee');
		$this->dropIndex('idx-employee-state_id', 'employee');
		$this->dropForeignKey('fk-employee-user_id', 'employee');
		$this->dropIndex('idx-employee-user_id', 'employee');
        $this->dropTable('{{%employee}}');
    }
}
