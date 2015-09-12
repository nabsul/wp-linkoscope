<?php

namespace app\controllers;

class LinkController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionVote()
    {
        return $this->render('vote');
    }

}
