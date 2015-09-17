<?php

namespace app\controllers;

use automattic\Rest\Org\OrgWpApi;
use yii\authclient\OAuthToken;
use yii\data\ArrayDataProvider;
use Yii;

class LinkController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $api = new OrgWpApi();
        $token = Yii::$app->session->get('token');
        $secret = Yii::$app->session->get('secret');
        $tok = new OAuthToken(['token' => $token, 'tokenSecret' => $secret]);
        $result = $api->getLinks($tok);
        $data = new ArrayDataProvider(['allModels' => $result]);
        return $this->render('index', [
            'data' => $data,
            'result' => $result,
        ]);
    }

    public function actionVote()
    {
        return $this->render('vote');
    }

    public function actionView($id)
    {
        $api = new OrgWpApi();
        $token = Yii::$app->session->get('token');
        $secret = Yii::$app->session->get('secret');
        $tok = new OAuthToken(['token' => $token, 'tokenSecret' => $secret]);
        //$api->deleteLink($tok);
        return $this->redirect(['index']);
    }

    public function actionUpdate($id)
    {
        $api = new OrgWpApi();
        $token = \Yii::$app->session->get('token');
        $secret = \Yii::$app->session->get('secret');
        $tok = new OAuthToken(['token' => $token, 'tokenSecret' => $secret]);
        //$api->deleteLink($tok);
        return $this->redirect(['index']);
    }

    public function actionDelete($id)
    {
        $api = new OrgWpApi();
        $token = \Yii::$app->session->get('token');
        $secret = \Yii::$app->session->get('secret');
        $tok = new OAuthToken(['token' => $token, 'tokenSecret' => $secret]);
        //$api->deleteLink($tok);
        return $this->redirect(['index']);
    }
}
