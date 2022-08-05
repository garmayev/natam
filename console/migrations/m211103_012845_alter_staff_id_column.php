<?php

use yii\db\Migration;

/**
 * Class m211103_012845_alter_staff_id_column
 */
class m211103_012845_alter_staff_id_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
//	    $this->renameColumn("{{%updates}}", "staff_id", "employee_id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
//	    $this->renameColumn("updates", "employee_id", "staff_id");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211103_012845_alter_staff_id_column cannot be reverted.\n";

        return false;
    }
    */
}
