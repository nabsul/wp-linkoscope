<?php
use yii\helpers\Html;

/* @var $model ShortCirquit\LinkoScopeApi\models\Comment */
?>

<div>
    <div>
        <?= ($index + 1) ?>:
    <?php if ($model->hasVoted) : ?>
        <?= Html::a('<span class="glyphicon glyphicon-arrow-down"></span>', ['down-comment', 'post' => $model->postId, 'id' => $model->id,], ['title' => 'Down']) ?>
    <?php else: ?>
        <?= Html::a('<span class="glyphicon glyphicon-arrow-up"></span>', ['up-comment', 'post' => $model->postId, 'id' => $model->id,], ['title' => 'Up']) ?>
    <?php endif; ?>
        <?= $model->content ?>
    </div>
</div>
<div>
    <?= "$model->authorName | ". date('D d M Y', strtotime($model->date)) . " | $model->votes votes" ?>
</div>
