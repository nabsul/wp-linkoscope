<?php

namespace app\controllers;

use automattic\Rest\Org\OrgWpApi;
use yii\helpers\Url;
use Yii;

class AdminController extends BaseController
{
    public function actionConnect()
    {
        return $this->render('connect');
    }

    public function actionIndex()
    {
        if ($this->getApi() != null)
            return $this->redirect(['config']);
        return $this->render('index');
    }

    public function actionConfig()
    {
        return $this->render('config', ['config' => $this->getApi()->getConfig()]);
    }

    public function actionWpCom()
    {
        return $this->render('wp-com');
    }

    public function actionWpOrg($oauth_token = null, $oauth_verifier = null, $wp_scope = null)
    {
        if ($oauth_token == null)
        {
            if (Yii::$app->request->isPost) {
                $cfg = Yii::$app->request->post();
                $cfg = array_intersect_key($cfg, ['blogUrl' => 0,'consumerKey' => 0, 'consumerSecret' => 0]);
                $api = new OrgWpApi($cfg);
                $this->saveConfig($api);

                $here = Url::to( '', true );
                $redirect = $api->authorize( $here );
                return $this->redirect( $redirect );
            }

            return $this->render('wp-org');
        }

        $api = new OrgWpApi();
        $tok = $api->access($oauth_token, $oauth_verifier);
        Yii::$app->session->set('token', $tok->token);
        Yii::$app->session->set('secret', $tok->tokenSecret);

        $types = $api->getTypes($tok);
        if (false === array_search('linkoscope_link', array_keys($types)))
        {
            Yii::$app->session->setFlash('error', 'Site does not support linkoscope post type.');
            return $this->redirect('index');
        }

        return $this->redirect(['link/index']);
    }
}
