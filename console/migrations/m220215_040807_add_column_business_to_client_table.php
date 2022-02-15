<?php

use yii\db\Migration;

/**
 * Class m220215_040807_add_column_business_to_client_table
 */
class m220215_040807_add_column_business_to_client_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220215_040807_add_column_business_to_client_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220215_040807_add_column_business_to_client_table cannot be reverted.\n";

        return false;
    }
    */
}
