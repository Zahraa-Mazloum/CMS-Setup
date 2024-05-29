<?php

namespace app\tiers\admin\controllers;

use yii\web\Controller;

class DefaultController extends Controller
{

    public function beforeAction($action)
    {
        $this->layout = "@resources/tiers/" . tier()->id . "/layouts/public.twig";
        if (in_array($action->id, ['login', 'logout'])) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        if (isAuth()) {
            return $this->redirect(['/panel/index']);
        }
        return $this->redirect(['login']);
    }

    public function actionLogin()
    {
        if (isAuth()) {
            return $this->redirect(['/panel/index']);
        }

        if (isPost()) {
            $redirectUrl = login(['/panel/index']);
            if ($redirectUrl) {
                return $this->redirect($redirectUrl);
            }
            flash("error", "Login Failed!");
            return $this->redirect(['/default/login']);
        }
        return $this->render('login.twig');
    }

    public function actionLogout()
    {
        logout();
        return $this->redirect(['/default/login']);
    }
}
