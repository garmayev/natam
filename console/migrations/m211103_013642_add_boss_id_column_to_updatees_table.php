<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%updatees}}`.
 */
class m211103_013642_add_boss_id_column_to_updatees_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%updates}}", "boss_id", $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropColumn("{{%updates}}", "boss_id");
    }
}
