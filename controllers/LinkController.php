<?php

namespace app\controllers;

use automattic\Rest\Org\OrgWpApi;
use yii\authclient\OAuthToken;
use yii\data\ArrayDataProvider;
use Yii;
use app\controllers\BaseController;

class LinkController extends BaseController
{
    public function actionIndex()
    {
        $api = $this->getApi();
        $result = $api->getLinks();
        $data = new ArrayDataProvider(['allModels' => $result]);
        return $this->render('index', [
            'data' => $data,
            'result' => $result,
        ]);
    }

    public function actionView($id)
    {
        $link = $this->getApi()->getLink($id);
        return $this->render('view', ['link' => $link]);
    }

    public function actionUpdate($id)
    {
        $link = $this->getApi()->getLink($id);
        return $this->render('update', ['link' => $link]);
    }

    public function actionDelete($id)
    {
        $this->getApi()->deleteLink($id);
        return $this->redirect(['index']);
    }

    public function actionUp($id)
    {

    }

    public function actionDown($id)
    {

    }
}
