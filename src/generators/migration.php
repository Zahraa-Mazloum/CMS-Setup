<?php

/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */
/* @var $className string the new migration class name */

echo <<< EOF
<?php

namespace app\migrations;

use app\components\inherits\db\Migration;

class {$className} extends Migration
{
    public function safeUp()
    {
        // TODO...

        \$this->createRelations();

        return true;
    }

    public function safeDown()
    {
        echo "{$className} cannot be reverted.\\n";

        return false;
    }
}
EOF;
