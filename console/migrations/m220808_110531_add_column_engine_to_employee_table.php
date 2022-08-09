<?php

use yii\db\Migration;

/**
 * Class m220808_110531_add_column_engine_to_employee_table
 */
class m220808_110531_add_column_engine_to_employee_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("employee", "engine", $this->double());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn("employee", "engine");
    }
}
