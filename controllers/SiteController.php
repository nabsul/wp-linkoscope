<?php

namespace app\controllers;

use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\controllers\BaseController;
use yii\helpers\Url;

class SiteController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionLogin($oauth_token = null, $oauth_verifier = null, $wp_scope = null)
    {
        $api = $this->getApi();
        if ($api == null) {
            Yii::$app->session->setFlash('error', 'The site is not configured yet.');
            $this->redirect(['admin/login']);
        }

        if ($oauth_token == null) {
            $here = Url::to('', true);
            $redirect = $api->authorize($here);
            return $this->redirect($redirect);
        }

        $tok = $api->access($oauth_token, $oauth_verifier);
        $user = $api->getAccount();

        $u = new User([
            'id' => $user['id'],
            'username' => $user['name'],
            'token' => $tok->token,
            'secret' => $tok->tokenSecret,
        ]);

        $u->saveSessionAccount();
        Yii::$app->user->login($u);

        return $this->redirect(['link/index']);
    }
}
