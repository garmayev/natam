<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%updates}}`.
 */
class m210910_015744_create_updates_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%updates}}', [
            'id' => $this->primaryKey(),
			'order_id' => $this->integer(),
	        'type' => $this->string(),
	        'per_time' => $this->integer(),
			'target_id' => $this->integer(),
	        'created_at' => $this->integer(),
	        'updated_at' => $this->integer(),
        ]);

		$this->createIndex(
			'idx-updates-order_id',
			'updates',
			'order_id'
		);
		$this->addForeignKey(
			'fk-updates-order_id',
			'updates',
			'order_id',
			'order',
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
		$this->dropForeignKey('fk-updates-order_id', 'updates');
        $this->dropTable('{{%updates}}');
    }
}
