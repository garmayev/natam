<?php

use yii2mod\rbac\migrations\Migration;

class m220214_060350_create_role_admin extends Migration
{
    public function safeUp()
    {
	    $this->createRole("person", "Физические лица");
	    $this->createRole("company", "Юридические лица");
	    $this->addChild("company", "person");
	    $this->createRole("employee", "Сотрудники могут работать с заказами и входящими запросами");
	    $this->addChild("employee", "person");
	    $this->addChild("employee", "company");
	    $this->createRole("admin", "Администратор имеет все права");
	    $this->addChild("admin", "person");
	    $this->addChild("admin", "company");
	    $this->addChild("admin", "employee");
    }

    public function safeDown()
    {
		$this->removeChild("admin", "employee");
	    $this->removeChild("admin", "company");
	    $this->removeChild("admin", "person");
		$this->removeRole("admin");
	    $this->removeChild("employee", "company");
	    $this->removeChild("employee", "person");
	    $this->removeRole("employee");
	    $this->removeChild("company", "person");
	    $this->removeRole("person");
	    $this->removeRole("company");
    }
}