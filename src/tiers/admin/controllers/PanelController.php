<?php

namespace app\tiers\admin\controllers;

use app\components\inherits\web\SController;

class PanelController extends SController
{
    public function beforeAction($action)
    {
        $this->layout = "@resources/tiers/" . tier()->id . "/layouts/main.twig";
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        return $this->render('index.twig');
    }

    public function actionAdmins()
    {
        return $this->render('admins.twig');
    }

    public function actionContents($type)
    {
        return $this->render('contents.twig', ['type' => $type]);
    }

    public function actionSeo()
    {
        return $this->render('seo.twig');
    }

    public function actionSettings()
    {
        return $this->render('settings.twig');
    }
}
