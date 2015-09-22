<?php

namespace app\controllers;

use app\models\CommentForm;
use app\models\LinkForm;
use automattic\Rest\Models\Comment;
use automattic\Rest\Models\Link;
use yii\data\ArrayDataProvider;
use Yii;

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

    public function actionView($link)
    {
        $api = $this->getApi();

        $form = new CommentForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate())
        {
            $comment = new Comment([
                'postId' => $link,
                'content' => $form->comment,
            ]);
            $api->addComment($comment);
            Yii::$app->session->setFlash('info', 'Your comment was added.');
            $this->redirect(['view', 'link'=>$link]);
        }

        $link = $api->getLink($link);
        $comments = $api->getComments($link);

        return $this->render('view', [
            'link' => $link,
            'comments' => new ArrayDataProvider(['allModels' => $comments]),
            'commentForm' => $form,
        ]);
    }

    public function actionUpdate($link = null)
    {
        $link = $this->getApi()->getLink($link);
        return $this->render('update', ['link' => $link]);
    }

    public function actionDelete($link = null, $comment = null)
    {
        $this->getApi()->deleteLink($link);
        return $this->redirect(['index']);
    }

    public function actionUp($link = null, $comment = null)
    {
        $api = $this->getApi();
        $link = $api->getLink($link);
        $link->votes++;
        $api->updateLink($link);
        return $this->redirect(['index']);
    }

    public function actionDown($link = null, $comment = null)
    {
        $api = $this->getApi();
        $link = $api->getLink($link);
        $link->votes--;
        $api->updateLink($link);
        return $this->redirect(['index']);
    }
}
