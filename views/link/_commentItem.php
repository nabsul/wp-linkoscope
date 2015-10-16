<?php
use yii\helpers\Html;

/* @var $model ShortCirquit\LinkoScopeApi\models\Comment */
?>

<div>
    <div>
        <?= ($index + 1) ?>: <?= $model->content ?>
    </div>
    <div style="top: 0px; float: right;">
        <?= Html::a('<span class="glyphicon glyphicon-arrow-up"></span>', ['up-comment', 'post' => $model->postId, 'id' => $model->id,], ['title' => 'Up']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-arrow-down"></span>', ['down-comment', 'post' => $model->postId, 'id' => $model->id,], ['title' => 'Down']) ?>
        <?= ''/*Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete-comment', 'post' => $model->postId, 'id' => $model->id,], ['title' => 'Delete'])*/ ?>
    </div>
</div>
<div>
    <?= "$model->authorName | ". date('D d M Y', strtotime($model->date)) . " | $model->votes votes" ?>
</div>
