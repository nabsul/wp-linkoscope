<?php

namespace app\controllers;

use app\models\LinkForm;
use automattic\Rest\Models\Link;
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

    public function actionNew()
    {
        $form = new LinkForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate())
        {
            $link = new Link([
                'title' => $form->title,
                'url' => $form->url,
            ]);

            $this->getApi()->addLink($link);
            return $this->redirect(['index']);
        }
        return $this->render('new', ['model' => $form]);
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
        $api = $this->getApi();
        $link = $api->getLink($id);
        $link->votes++;
        $api->updateLink($link);
        return $this->redirect(['index']);
    }

    public function actionDown($id)
    {
        $api = $this->getApi();
        $link = $api->getLink($id);
        $link->votes--;
        $api->updateLink($link);
        return $this->redirect(['index']);
    }
}
