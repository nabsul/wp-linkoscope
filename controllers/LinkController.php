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
use ShortCirquit\LinkoScopeApi\iLinkoScope;
use yii\web\Controller;

class LinkController extends Controller
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
                            return !Yii::$app->user->isGuest && Yii::$app->user->id != 'admin';
                        },
                    ],
                ],
                'denyCallback' => function($r, InlineAction $a){
                    if (Yii::$app->user->isGuest)
                        Yii::$app->session->setFlash('info', 'You must be logged in to do that.');
                    else
                        Yii::$app->session->setFlash('info', 'Please log out of the admin account and log in with a regular user account.');
                    $a->controller->redirect(['link/index']);
                }
            ],
        ];
    }

    public function actionIndex($page = null, $pageSize = null)
    {
        $pageSize = $pageSize ?: Yii::$app->params['pageSize'];
        $page = $page ?: 1;
        $offset = ($page - 1) * $pageSize;
        $form = new LinkForm();
        $form->title = '__AUTO__';

        $req = new GetLinksRequest();
        $req->offset = $offset;
        $req->maxResults = $pageSize;
        $result = Yii::$app->linko->getApi()->getLinks($req);

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

    public function actionUser($id, $page = null, $pageSize = null)
    {
        $pageSize = $pageSize ?: Yii::$app->params['pageSize'];
        $page = $page ?: 1;
        $offset = ($page - 1) * $pageSize;
        $form = new LinkForm();
        $form->title = '__AUTO__';

        /** @var iLinkoScope $api */
        $api = Yii::$app->linko->getApi();

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
            Yii::$app->linko->getApi()->addLink($link);
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

            Yii::$app->linko->getApi()->updateLink($link);
            return $this->redirect(['view', 'id' => $id]);
        }

        $link = Yii::$app->linko->getApi()->getLink($id);
        $form->url = $link->url;
        $form->title = $link->title;
        return $this->render('new', ['model' => $form]);
    }

    public function actionView($id)
    {
        /** @var iLinkoScope $api */
        $api = Yii::$app->linko->getApi();

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
        Yii::$app->linko->getApi()->getLink($id);
        return $this->render('update', ['id' => $id]);
    }

    public function actionDelete($id)
    {
        Yii::$app->linko->getApi()->deleteLink($id);
        return $this->redirect(['index']);
    }

    public function actionUp($id)
    {
        Yii::$app->linko->getApi()->likeLink($id, Yii::$app->user->id);
        return $this->redirect(['index']);
    }

    public function actionDown($id)
    {
        Yii::$app->linko->getApi()->unlikeLink($id, Yii::$app->user->id);
        return $this->redirect(['index']);
    }

    public function actionUpComment($post, $id)
    {
        Yii::$app->linko->getApi()->likeComment($id, Yii::$app->user->id);
        return $this->redirect(['view', 'id' => $post]);
    }

    public function actionDownComment($post, $id)
    {
        Yii::$app->linko->getApi()->unlikeComment($id, Yii::$app->user->id);
        return $this->redirect(['view', 'id' => $post]);
    }

    public function actionDeleteComment($post, $id)
    {
        Yii::$app->linko->getApi()->deleteComment($id);
        return $this->redirect(['view', 'id' => $post]);
    }
}
