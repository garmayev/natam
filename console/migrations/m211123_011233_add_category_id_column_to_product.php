<?php

use yii\db\Migration;

/**
 * Class m211123_011233_add_category_id_column_to_product
 */
class m211123_011233_add_category_id_column_to_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%product}}", "category_id", $this->integer());
		$this->createIndex(
			"idx-product-category_id",
			"{{%product}}",
			"category_id"
		);
		$this->addForeignKey(
			"fk-product-category_id",
			"{{%product}}",
			"category_id",
			"{{%category}}",
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
		$this->dropForeignKey("fk-product-category_id", "{{%product}}");
		$this->dropIndex("idx-product-category_id", "{{%product}}");
	    $this->dropColumn("{{%product}}", "category_id");
    }
}
