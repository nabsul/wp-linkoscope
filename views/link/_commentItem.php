<?php
use yii\helpers\Html;

/* @var $model ShortCirquit\LinkoScopeApi\models\Comment */
/* @var $index int */
?>

<div>
    <div>
        <?= ($index + 1) ?>:
        <?php if (!Yii::$app->user->isGuest) : ?>
            <?php if ($model->hasVoted) : ?>
                <?= Html::a(
                    '<span class="glyphicon glyphicon-arrow-down"></span>',
                    ['down-comment', 'post' => $model->postId, 'id' => $model->id,], ['title' => 'Down']
                ) ?>
            <?php else: ?>
                <?= Html::a(
                    '<span class="glyphicon glyphicon-arrow-up"></span>',
                    ['up-comment', 'post' => $model->postId, 'id' => $model->id,], ['title' => 'Up']
                ) ?>
            <?php endif; ?>
        <?php endif; ?>
        <span class='main-link'><?= $model->content ?></span>
    </div>
</div>
<div>
    <?= "$model->authorName | " . date('D, d M Y H:i:s', strtotime($model->date)) . " | $model->votes votes" ?>
</div>
