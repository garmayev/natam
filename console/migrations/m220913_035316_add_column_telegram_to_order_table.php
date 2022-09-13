<?php

use yii\db\Migration;

/**
 * Class m220913_035316_add_column_telegram_to_order_table
 */
class m220913_035316_add_column_telegram_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('order', 'telegram', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('order', 'telegram');
    }
}
