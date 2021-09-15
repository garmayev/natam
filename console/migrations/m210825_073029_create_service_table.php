<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service}}`.
 */
class m210825_073029_create_service_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%service}}', [
            'id' => $this->primaryKey(),
	        'title' => $this->string(),
	        'description' => $this->text(),
	        'thumbs' => $this->string(),
	        'parent_id' => $this->integer(),
        ]);

        $this->insert('{{%service}}', [
        	'title' => 'Дополнительные услуги',
	        'description' => null,
	        'thumbs' => '/img/services-1.png',
	        'parent_id' => null,
        ]);

	    $this->insert('{{%service}}', [
		    'title' => 'Поставка оборудования и комплектующих',
		    'description' => null,
		    'thumbs' => '/img/services-2.png',
		    'parent_id' => null,
	    ]);

	    $this->insert('{{%service}}', [
		    'title' => 'Переаттестация и ремонт баллонов',
		    'description' => null,
		    'thumbs' => '/img/services-3.png',
		    'parent_id' => null,
	    ]);

	    $this->insert('{{%service}}', [
		    'title' => 'Перевозка опасных грузов',
		    'description' => null,
		    'thumbs' => '/img/services-4.png',
		    'parent_id' => null,
	    ]);

	    $this->insert('{{%service}}', [
		    'title' => 'Разработка проекта и монтаж оборудования',
		    'description' => null,
		    'thumbs' => '/img/services-5.png',
		    'parent_id' => null,
	    ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service}}');
    }
}
