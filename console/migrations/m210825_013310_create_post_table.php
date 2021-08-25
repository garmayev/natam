<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%post}}`.
 */
class m210825_013310_create_post_table extends Migration
{
	public $tableName = 'post';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("{{%$this->tableName}}", [
            'id' => $this->primaryKey(),
	        'title' => $this->string(128),
	        'description' => $this->text(),
	        'thumbs' => $this->string(),
	        'author_id' => $this->integer(),
	        'created_at' => $this->integer(),
        ]);

        $this->createIndex(
        	"idx-$this->tableName-author_id",
	        "{{%$this->tableName}}",
	        "author_id"
        );

        $this->addForeignKey(
        	"fk-$this->tableName-author_id",
	        "{{%$this->tableName}}",
	        "author_id",
	        "{{%user}}",
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
    	$this->dropForeignKey("fk-$this->tableName-author_id", "{{%$this->tableName}}");
    	$this->dropIndex("idx-$this->tableName-author_id", "{{%$this->tableName}}");
        $this->dropTable('{{%post}}');
    }
}
