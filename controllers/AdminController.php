<?php

namespace app\controllers;

class AdminController extends \yii\web\Controller
{
    public function actionConnect()
    {
        return $this->render('connect');
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionWpCom()
    {
        return $this->render('wp-com');
    }

    public function actionWpOrg()
    {
        return $this->render('wp-org');
    }

}
