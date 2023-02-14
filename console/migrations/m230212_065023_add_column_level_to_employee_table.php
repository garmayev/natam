<?php

use yii\db\Migration;

/**
 * Class m230212_065023_add_column_level_to_employee_table
 */
class m230212_065023_add_column_level_to_employee_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('employee', 'level', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('employee', 'level');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230212_065023_add_column_level_to_employee_table cannot be reverted.\n";

        return false;
    }
    */
}
