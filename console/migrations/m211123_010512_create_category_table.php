<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%category}}`.
 */
class m211123_010512_create_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%category}}', [
            'id' => $this->primaryKey(),
	        'title' => $this->string(),
	        'content' => $this->text(),
	        'thumbs' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%category}}');
    }
}
