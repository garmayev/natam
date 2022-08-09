<?php

use yii\db\Migration;

/**
 * Class m220809_035602_add_column_article_to_order_table
 */
class m220809_035602_add_column_article_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("order", "article", $this->string());
        $this->addColumn("company", "inn", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("order", "article");
        $this->dropColumn("company", "inn");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220809_035602_add_column_article_to_order_table cannot be reverted.\n";

        return false;
    }
    */
}
