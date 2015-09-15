<?php

namespace app\controllers;

use automattic\Rest\Org\OrgWpApi;

class LinkController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $api = new OrgWpApi();
        return $this->render('index');
    }

    public function actionVote()
    {
        return $this->render('vote');
    }

}
