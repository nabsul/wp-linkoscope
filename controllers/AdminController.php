<?php

namespace app\controllers;

use automattic\Rest\Org\OrgWpApi;
use yii\helpers\Url;
use Yii;

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

    public function actionWpOrg($oauth_token = null, $oauth_verifier = null, $wp_scope = null)
    {
        if ($oauth_token == null)
        {
            $return = Url::to('', true);
            $api = new OrgWpApi();
            $redirect = $api->authorize($return);
            return $this->redirect($redirect);
        }

        $result = "Yay!\n$oauth_token\n$oauth_verifier\n$wp_scope";

        $api = new OrgWpApi();

        $tok = $api->access($oauth_token, $oauth_verifier);
        $result .= "\n" . $tok->token;

        $types = $api->getTypes($tok);
        if (false === array_search('linkoscope_link', array_keys($types)))
        {
            $result .= "\ntype not found";
        }
        else
        {
            $result .= "\ntype is found";
        }

        Yii::$app->session->set('token', $tok->token);
        Yii::$app->session->set('secret', $tok->tokenSecret);
        $result .= "\nToken saved in session";
        return $this->render('wp-org', ['result' => $result]);
    }
}
