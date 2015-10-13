<?php

namespace app\controllers;

use app\models\User;
use ShortCirquit\LinkoScopeApi\ComLinkoScope;
use ShortCirquit\LinkoScopeApi\OrgLinkoScope;
use ShortCirquit\WordPressApi\ComWpApi;
use ShortCirquit\WordPressApi\OrgWpApi;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\HttpException;

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

    public function actionLogin($code = null, $error = null, $oauth_token = null,
                                $oauth_verifier = null, $wp_scope = null)
    {
        $api = $this->getApi();
        if ($api == null) {
            Yii::$app->session->setFlash('error', 'The site is not configured yet.');
            $this->redirect(['admin/login']);
        }

        switch (get_class($api))
        {
            case 'ShortCirquit\LinkoScopeApi\ComLinkoScope':
                return $this->loginCom($code, $error);
            case 'ShortCirquit\LinkoScopeApi\OrgLinkoScope':
                return $this->loginOrg($oauth_token, $oauth_verifier, $wp_scope);
            default:
                throw new HttpException(500, 'Unexpected WpApi class type: ' . get_class($api));
        }
    }

    private function loginCom($code = null, $error = null)
    {
        if ($error != null) {
            throw new HttpException(301, "Error: $error");
        }

        if ($code == null) {
            return $this->redirect($this->getApi()->authorize());
        }

        $api = $this->getApi();
        $auth = $api->token($code);

        if (is_string($auth))
        {
            throw new HttpException(301, "Failed to get token with error: $auth");
        }

        $redirect = Yii::$app->session->get('login-com', false);
        if ($redirect !== false) {
            $cfg = [
                    'blogUrl' => $auth['blog_url'],
                    'blogId' => $auth['blog_id'],
                    'adminToken' => $auth['access_token'],
                ] + $api->getConfig();
            $api = new ComLinkoScope($cfg);
            $this->saveConfig($api);
            Yii::$app->session->setFlash('info', 'Successfully completed WP.com config for: ' . $auth['blog_url']);
            return $this->redirect([$redirect]);
        }

        $cfg = $api->getConfig() + ['token' => $auth['access_token']];
        $api = new ComLinkoScope($cfg);
        $account = $api->getAccount();

        $u = new User([
            'id' => $account['ID'],
            'username' => $account['display_name'],
            'token' => $auth['access_token'],
        ]);

        $u->saveSessionAccount();
        Yii::$app->user->login($u);

        return $this->redirect(['link/index']);
    }

    private function loginOrg($oauth_token = null, $oauth_verifier = null, $wp_scope = null)
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
        $cfg = $api->getConfig();
        $cfg['accessToken'] = $tok['oauth_token'];
        $cfg['accessTokenSecret'] = $tok['oauth_token_secret'];
        $api = new OrgLinkoScope($cfg);
        $user = $api->getAccount();
        $u = new User([
            'id' => $user['body']['id'],
            'username' => $user['body']['name'],
            'token' => $tok['oauth_token'],
            'secret' => $tok['oauth_token_secret'],
        ]);

        $u->saveSessionAccount();
        Yii::$app->user->login($u);

        return $this->redirect(['link/index']);
    }
}
