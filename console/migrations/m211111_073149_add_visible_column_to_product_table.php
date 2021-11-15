<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%product}}`.
 */
class m211111_073149_add_visible_column_to_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	$this->addColumn("{{%product}}", "visible", $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
	$this->dropColumn("{{%product}}", "visible");
    }
}
