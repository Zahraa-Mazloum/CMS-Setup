<?php

namespace app\migrations;

use app\components\inherits\db\Migration;

class M230823082008AddFriendlyUrlTable extends Migration
{
    public function safeUp()
    {
        $this->createTable("friendly_url", [
            'id' => $this->primaryKey(),
            'target' => $this->string()->unique(),
            'name' => $this->string(),
            'pattern' => $this->string()->unique(),
            'route' => $this->string(),
            'params' => $this->json(),
        ]);

        $this->createRelations();

        return true;
    }

    public function safeDown()
    {
        echo "M230823082008AddFriendlyUrlTable cannot be reverted.\n";

        return false;
    }
}
