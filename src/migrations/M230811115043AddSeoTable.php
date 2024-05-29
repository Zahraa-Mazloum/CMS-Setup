<?php

namespace app\migrations;

use app\components\inherits\db\Migration;

class M230811115043AddSeoTable extends Migration
{
    public function safeUp()
    {
        $this->createTable('seo', [
            'id' => $this->primaryKey(),
            'target' => $this->string()->unique(),
            'title' => $this->string(),
            'description' => $this->text(),
            'keywords' => $this->text(),
            'is_default' => $this->boolean(),
        ]);

        $this->createRelations();

        return true;
    }

    public function safeDown()
    {
        echo "M230811115043AddSeoTable cannot be reverted.\n";

        return false;
    }
}
