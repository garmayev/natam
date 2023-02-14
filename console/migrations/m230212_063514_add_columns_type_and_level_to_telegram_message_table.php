<?php

use yii\db\Migration;

/**
 * Class m230212_063514_add_columns_type_and_level_to_telegram_message_table
 */
class m230212_063514_add_columns_type_and_level_to_telegram_message_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('telegram_message', 'type', $this->integer());
        $this->addColumn('telegram_message', 'level', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('telegram_message', 'type');
        $this->dropColumn('telegram_message', 'level');
        echo "m230212_063514_add_columns_type_and_level_to_telegram_message_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230212_063514_add_columns_type_and_level_to_telegram_message_table cannot be reverted.\n";

        return false;
    }
    */
}
