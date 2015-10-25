<?php
use yii\helpers\Html;

/* @var $model ShortCirquit\LinkoScopeApi\models\Link */
/* @var $index int */
?>

<div>
    <div>
        <?= ($index + 1) ?>:
        <?php if (!Yii::$app->user->isGuest) : ?>
            <?php if ($model->hasVoted) : ?>
                <?= Html::a(
                    '<span class="glyphicon glyphicon-arrow-down"></span>', ['down', 'id' => $model->id,],
                    ['title' => 'Down']
                ) ?>
            <?php else : ?>
                <?= Html::a(
                    '<span class="glyphicon glyphicon-arrow-up"></span>', ['up', 'id' => $model->id,], ['title' => 'Up']
                ) ?>
            <?php endif; ?>
        <?php endif; ?>
        <span class="main-link">
            [<?= Html::a($model->title, $model->url, ['target' => '_blank',]); ?>]
        </span>
                           (<?= parse_url($model->url, PHP_URL_HOST) ?>)
    </div>
    <?php if (!Yii::$app->user->isGuest) : ?>
        <div style="top: 0; float: right;">
            <?= '' /* Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model->id,], ['title' => 'Delete']) */ ?>
        </div>
    <?php endif; ?>
</div>
<div>
    <?=
    Html::a($model->authorName, ['user', 'id' => $model->authorId]) . " | " .
    date('D d M Y', strtotime($model->date)) .
    " | $model->votes votes | " .
    Html::a($model->comments == 0 ? "discuss" : "$model->comments comments", ['link/view', 'id' => $model->id])
    ?>
</div>
