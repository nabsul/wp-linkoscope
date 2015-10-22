<?php

namespace app\controllers;

use app\models\CommentForm;
use app\models\LinkForm;
use ShortCirquit\LinkoScopeApi\GetLinksRequest;
use ShortCirquit\LinkoScopeApi\Models\Comment;
use ShortCirquit\LinkoScopeApi\Models\Link;
use yii\data\ArrayDataProvider;
use Yii;
use yii\filters\AccessControl;
use yii\base\InlineAction;
use app\components\Crawler;

class LinkController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['index', 'view'],
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function($r, $a){
                            return !Yii::$app->user->isGuest;
                        },
                    ],
                ],
                'denyCallback' => function($r, InlineAction $a){
                    Yii::$app->session->setFlash('info', 'You must be logged in to do that.');
                    $a->controller->redirect(['link/index']);
                }
            ],
        ];
    }

    public function actionIndex($page = null)
    {
        $pageSize = Yii::$app->params['pageSize'];
        $page = $page ?: 1;
        $offset = ($page - 1) * $pageSize;
        $form = new LinkForm();
        $form->title = '__AUTO__';

        $req = new GetLinksRequest();
        $req->offset = $offset;
        $req->maxResults = $pageSize;
        $result = $this->getApi()->getLinks($req);

        $data = new ArrayDataProvider([
            'totalCount' => $result->totalResults,
            'models' => $result->links,
            'pagination' => [
                'totalCount' => $result->totalResults,
                'defaultPageSize' => $pageSize,
            ],
        ]);

        return $this->render('index', [
            'data' => $data,
            'linkForm' => $form,
        ]);
    }

    public function actionUser($id, $page = null)
    {
        $pageSize = Yii::$app->params['pageSize'];
        $page = $page ?: 1;
        $offset = ($page - 1) * $pageSize;
        $form = new LinkForm();
        $form->title = '__AUTO__';

        $api = $this->getApi();

        $req = new GetLinksRequest();
        $req->offset = $offset;
        $req->maxResults = $pageSize;
        $req->authorId = $id;
        $result = $api->getLinks($req);
        $user = $api->getAccount($id);

        $data = new ArrayDataProvider([
            'totalCount' => $result->totalResults,
            'models' => $result->links,
            'pagination' => [
                'totalCount' => $result->totalResults,
                'defaultPageSize' => $pageSize,
            ],
        ]);

        return $this->render('user', [
            'data' => $data,
            'user' => $user,
        ]);
    }

    public function actionNew()
    {
        $form = new LinkForm();
        if (!$form->load(Yii::$app->request->post()))
            return $this->render('new', ['linkForm' => $form]);

        if ($form->title == '__AUTO__')
        {
            $form->title = null;
            if (preg_match('/^https?:\/\//', $form->url))
                $form->title = Yii::$app->crawler->readTitle($form->url);
            return $this->render('new', ['linkForm' => $form]);
        }

        if ($form->validate()){
            $link = new Link([
                'title' => $form->title,
                'url' => $form->url,
                'authorId' => Yii::$app->user->identity->getId(),
            ]);
            $this->getApi()->addLink($link);
            Yii::$app->session->setFlash('info', 'Your link has been added.');
            return $this->redirect(['index']);
        }

        if ($form->hasErrors()){
            Yii::$app->session->setFlash('error', implode('<br />', $form->getFirstErrors()));
        }

        return $this->render('new', ['linkForm' => $form]);
    }

    public function actionEdit($id)
    {
        $form = new LinkForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate())
        {
            $link = new Link([
                'title' => $form->title,
                'url' => $form->url,
                'id' => $id
            ]);

            $this->getApi()->updateLink($link);
            return $this->redirect(['view', 'id' => $id]);
        }

        $link = $this->getApi()->getLink($id);
        $form->url = $link->url;
        $form->title = $link->title;
        return $this->render('new', ['model' => $form]);
    }

    public function actionView($id)
    {
        $api = $this->getApi();

        $form = new CommentForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate())
        {
            $comment = new Comment([
                'postId' => $id,
                'content' => $form->comment,
                'authorId' => Yii::$app->user->id,
                'authorName' => Yii::$app->user->getIdentity()->username
            ]);

            $api->addComment($comment);
            Yii::$app->session->setFlash('info', 'Your comment was added.');
            $this->redirect(['view', 'id'=>$id]);
        }

        if ($form->hasErrors()){
            Yii::$app->session->setFlash('error', implode('<br />', $form->getFirstErrors()));
        }

        $linkObject = $api->getLink($id);
        $comments = $api->getComments($id);

        return $this->render('view', [
            'link' => $linkObject,
            'comments' => new ArrayDataProvider(['allModels' => $comments]),
            'commentForm' => $form,
        ]);
    }

    public function actionUpdate($id)
    {
        $this->getApi()->getLink($id);
        return $this->render('update', ['id' => $id]);
    }

    public function actionDelete($id)
    {
        $this->getApi()->deleteLink($id);
        return $this->redirect(['index']);
    }

    public function actionUp($id)
    {
        $this->getApi()->likeLink($id, Yii::$app->user->id);
        return $this->redirect(['index']);
    }

    public function actionDown($id)
    {
        $this->getApi()->unlikeLink($id, Yii::$app->user->id);
        return $this->redirect(['index']);
    }

    public function actionUpComment($post, $id)
    {
        $this->getApi()->likeComment($id, Yii::$app->user->id);
        return $this->redirect(['view', 'id' => $post]);
    }

    public function actionDownComment($post, $id)
    {
        $this->getApi()->unlikeComment($id, Yii::$app->user->id);
        return $this->redirect(['view', 'id' => $post]);
    }

    public function actionDeleteComment($post, $id)
    {
        $this->getApi()->deleteComment($id);
        return $this->redirect(['view', 'id' => $post]);
    }
}
