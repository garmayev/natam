<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%category}}`.
 */
class m221026_080039_add_column_to_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('category', 'sort', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
