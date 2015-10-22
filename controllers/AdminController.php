<?php

namespace app\controllers;

use app\models\User;
use app\models\WpOrgConfigForm;
use app\models\WpComConfigForm;
use ShortCirquit\LinkoScopeApi\ComLinkoScope;
use ShortCirquit\LinkoScopeApi\OrgLinkoScope;
use yii\base\InlineAction;
use yii\filters\AccessControl;
use yii\helpers\Url;
use Yii;
use app\models\LoginForm;
use yii\web\Controller;
use yii\web\HttpException;

class AdminController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['login'],
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function($r, $a){
                            return !Yii::$app->user->isGuest && Yii::$app->user->id == 'admin';
                        },
                    ],
                ],
                'denyCallback' => function($r, InlineAction $a){
                    $a->controller->redirect(['admin/login']);
                }
            ],
        ];
    }

    public function actionConnect()
    {
        return $this->render('connect');
    }

    public function actionIndex()
    {
        Yii::$app->linko->readConfig();
        return $this->render('index');
    }

    public function actionConfig()
    {
        return $this->render('config', ['config' => $this->getApi()->getConfig()]);
    }

    public function actionWpCom()
    {
        $form = new WpComConfigForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            Yii::$app->linko->config =  [
                'clientId' => $form->clientId,
                'clientSecret' => $form->clientSecret,
                'redirectUrl' => Url::to( ['site/login'], true ),
                'type' => 'ShortCirquit\LinkoScopeApi\ComLinkoScope',
            ];
            Yii::$app->linko->saveConfig();

            Yii::$app->session->set('login-com', 'admin/index');
            return $this->redirect( ['site/login'] );
        }

        return $this->render('wp-com', ['model' => $form]);
    }

    public function actionWpOrg($oauth_token = null, $oauth_verifier = null, $wp_scope = null)
    {
        if ($oauth_token == null)
        {
            $form = new WpOrgConfigForm();
            if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                Yii::$app->linko->config = [
                    'consumerKey' => $form->consumerKey,
                    'consumerSecret' => $form->consumerSecret,
                    'blogUrl' => $form->blogUrl,
                    'type' => 'ShortCirquit\LinkoScopeApi\OrgLinkoScope',
                ];
                Yii::$app->linko->saveConfig();
                Yii::$app->linko->readConfig();

                /** @var OrgLinkoScope $api */
                $api = Yii::$app->linko->getApi();

                $here = Url::to( '', true );
                $redirect = $api->authorize( $here );
                return $this->redirect( $redirect );
            }

            return $this->render('wp-org', ['model' => $form]);
        }

        /** @var OrgLinkoScope $api */
        $api = Yii::$app->linko->getApi();
        $tok = $api->access($oauth_token, $oauth_verifier);
        Yii::$app->linko->config['adminToken'] = $tok['oauth_token'];
        Yii::$app->linko->config['adminSecret'] = $tok['oauth_token_secret'];
        Yii::$app->linko->saveConfig();

        return $this->redirect(['index']);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if (!User::adminConfigured())
        {
            throw new HttpException(403, 'Admin account not configured.');
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['index']);
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }
}
