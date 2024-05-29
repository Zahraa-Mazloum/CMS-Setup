<?php

namespace app\commands;

use app\models\User;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\Inflector;
use app\enums\UserStatus;
use app\lib\Jwt;
use app\models\Admin;
use app\models\FriendlyUrl;

class MakeController extends Controller
{
    public function actionModel($tableName)
    {
        $modelClass = Inflector::id2camel($tableName, '_');
        $queryClass = $modelClass . 'Query';
        $output = shell_exec("php yii gii/model --tableName={$tableName} --modelClass={$modelClass} --queryClass={$queryClass} --ns=app\\models --queryNs=app\\queries --baseClass=app\\components\\inherits\\db\\ActiveRecord --queryBaseClass=app\\components\\inherits\\db\\ActiveQuery  --generateQuery=1 --singularize=1 --standardizeCapitals=1 --useTablePrefix=1 --template=jawdat_model --interactive=0");
        echo "\n\n" . $output . "\n\n";
        return 0;
    }

    public function actionUser()
    {
        $name = Console::prompt(Console::ansiFormat('Name : ', [Console::FG_GREEN]), ['required' => true]);
        $username = Console::prompt(Console::ansiFormat('Username : ', [Console::FG_YELLOW]), ['required' => true]);
        $password = Console::prompt(Console::ansiFormat('Password : ', [Console::FG_RED]), ['required' => true]);
        $type = Console::select(Console::ansiFormat('User Type : ', [Console::FG_GREEN], ['required' => true]), [
            'developer' => 'Developer',
            'master' => 'Master Admin',
            'admin' => 'Admin',
        ]);

        if (Console::confirm("Create new user : $name | $username | $password | $type")) {
            if ($name && $username && $password && $type) {
                $user = new User();
                $user->username = $username;
                $user->setPassword($password);
                $user->type = $type;
                $user->status = UserStatus::ACTIVE->value;
                if ($user->save()) {
                    $admin = new Admin();
                    $admin->id = $user->id;
                    $admin->name = $name;
                    if ($admin->save()) {
                        echo Console::ansiFormat("User Created!", [Console::FG_GREEN]) . "\n";
                    } else {
                        echo Console::ansiFormat("Admin Creation Error : ", [Console::FG_RED]) . "\n";
                        print_r($admin->errors);
                    }
                } else {
                    echo Console::ansiFormat("User Creation Error : ", [Console::FG_RED]) . "\n";
                    print_r($user->errors);
                }
            }
        }
        return 0;
    }

    public function actionCreateJwtKey()
    {
        $token = new Jwt();
        $secret = $token->generateKey();
        echo "Secret:\n";
        echo $secret;
        echo "\nCopy this key to the .env file like this:\n";
        echo "'jwt.key.secret'=>'" . $secret . "',\n";
    }

    public function actionSyncContents()
    {
        cms()->syncContents(callback: function (string $text) {
            echo $text;
        });
    }

    public function actionFriendlyUrl()
    {
        FriendlyUrl::generateRules();
    }
}
