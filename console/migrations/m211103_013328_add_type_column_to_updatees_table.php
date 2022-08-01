<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%updatees}}`.
 */
class m211103_013328_add_type_column_to_updatees_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn("{{%updates}}", "type", $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropColumn("{{%updates}}", "type");
    }
}
