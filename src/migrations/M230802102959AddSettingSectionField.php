<?php

namespace app\migrations;

use app\components\inherits\db\Migration;

class M230802102959AddSettingSectionField extends Migration
{
    public function safeUp()
    {
        $this->addColumn('setting', 'section', $this->string()->after('type'));

        $this->createRelations();

        return true;
    }

    public function safeDown()
    {
        echo "M230802102959AddSettingSectionField cannot be reverted.\n";

        return false;
    }
}
