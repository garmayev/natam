<?php

use yii\db\Migration;

/**
 * Class m220708_062711_add_column_car_to_employee_table
 */
class m220708_062711_add_column_car_to_employee_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('employee', 'car', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropColumn('employee', 'car');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220708_062711_add_column_car_to_employee_table cannot be reverted.\n";

        return false;
    }
    */
}
