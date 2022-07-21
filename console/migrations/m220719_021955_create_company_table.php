<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%company}}`.
 */
class m220719_021955_create_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%company}}', [
            'id' => $this->primaryKey(),
	        'title' => $this->string(),
	        'kpp' => $this->string(),
	        'ogrn' => $this->string(),
	        'bik' => $this->string(),
	        'boss_id' => $this->integer(),
        ]);

		$this->createIndex('idx-company-boss_id', 'company', 'boss_id');
		$this->addForeignKey('fk-company-boss_id', 'company', 'boss_id', 'client', 'id');

		$this->addColumn('client', 'company_id', $this->integer());

		$this->createIndex('idx-client-company_id', 'client', 'company_id');
		$this->addForeignKey('fk-client-company_id', 'client', 'company_id', 'company', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropForeignKey('fk-client-company_id', 'client');
		$this->dropIndex('idx-client-company_id', 'client');
		$this->dropColumn('client', 'company_id');

		$this->dropForeignKey('fk-company-boss_id', 'company');
		$this->dropIndex('idx-company-boss_id', 'company');
        $this->dropTable('{{%company}}');
    }
}
