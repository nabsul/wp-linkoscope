<?php

namespace app\controllers;

use app\models\CommentForm;
use app\models\LinkForm;
use ShortCirquit\LinkoScopeApi\Models\Comment;
use ShortCirquit\LinkoScopeApi\Models\Link;
use yii\data\ArrayDataProvider;
use Yii;
use yii\log\Logger;

class LinkController extends BaseController
{
    public function actionIndex()
    {
        $result = $this->getApi()->getLinks();
        Yii::getLogger()->log(json_encode($result), Logger::LEVEL_INFO);
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
                'authorId' => Yii::$app->user->identity->getId(),
            ]);

            $this->getApi()->addLink($link);
            return $this->redirect(['index']);
        }
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
            ]);

            $api->addComment($comment);
            Yii::$app->session->setFlash('info', 'Your comment was added.');
            $this->redirect(['view', 'id'=>$id]);
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
