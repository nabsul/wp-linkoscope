<?php

namespace app\controllers;

use app\models\WpOrgConfigForm;
use app\models\WpComConfigForm;
use automattic\Rest\Com\ComWpApi;
use automattic\Rest\Org\OrgWpApi;
use yii\base\InlineAction;
use yii\filters\AccessControl;
use yii\helpers\Url;
use Yii;
use app\models\LoginForm;
use yii\web\HttpException;

class AdminController extends BaseController
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
        return $this->render('index',['api'=>$this->getApi()]);
    }

    public function actionConfig()
    {
        return $this->render('config', ['config' => $this->getApi()->getConfig()]);
    }

    public function actionWpCom($code = null, $error = null)
    {
        if ($error != null)
        {
            throw new HttpException(301, "Error: $error");
        }

        if ($code == null)
        {
            $form = new WpComConfigForm();
            if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                $api = new ComWpApi(array_merge(
                    $form->getConfig(),
                    ['redirectUrl' => Url::to( '', true )]
                ));
                $this->saveConfig($api);

                $redirect = $api->authorize( $api->redirectUrl );
                return $this->redirect( $redirect );
            }

            return $this->render('wp-com', ['model' => $form]);
        }

        $api = $this->getApi();
        $auth = $api->token($code);

        if ($auth !== true)
        {
            throw new HttpException(301, "Failed to get token with error: $auth");
        }

        $this->saveConfig($api);
        return $this->redirect(['index']);
    }

    public function actionWpOrg($oauth_token = null, $oauth_verifier = null, $wp_scope = null)
    {
        if ($oauth_token == null)
        {
            $form = new WpOrgConfigForm();
            if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                $api = new OrgWpApi($form->getConfig());
                $this->saveConfig($api);

                $here = Url::to( '', true );
                $redirect = $api->authorize( $here );
                return $this->redirect( $redirect );
            }

            return $this->render('wp-org', ['model' => $form]);
        }

        $api = new OrgWpApi();
        $tok = $api->access($oauth_token, $oauth_verifier);

        $types = $api->getTypes($tok);
        if (false === array_search('linkoscope_link', array_keys($types)))
        {
            Yii::$app->session->setFlash('error', 'Site does not support linkoscope post type.');
            return $this->redirect('index');
        }

        return $this->redirect(['link/index']);
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }
}
