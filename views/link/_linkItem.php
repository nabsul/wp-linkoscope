<?php
use yii\helpers\Html;

/* @var $model ShortCirquit\LinkoScopeApi\models\Link */
?>

<div>
    <div>
        <?= ($index + 1) ?>:
        [<?= Html::a($model->title, $model->url, ['target' => '_blank',]); ?>]
        (<?= parse_url($model->url, PHP_URL_HOST) ?>)
    </div>
<?php if (!Yii::$app->user->isGuest) : ?>
    <div style="top: 0px; float: right;">
        <?= Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $model->id,], ['title' => 'View']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-arrow-up"></span>', ['up', 'id' => $model->id,], ['title' => 'Up']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-arrow-down"></span>', ['down', 'id' => $model->id,], ['title' => 'Down']) ?>
        <?= '' /* Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model->id,], ['title' => 'Delete']) */ ?>
    </div>
<?php endif; ?>
</div>
<div>
    <?= "$model->authorName | ". date(DATE_RFC2822, strtotime($model->date)) . " | $model->votes votes | $model->comments comments | $model->score points" ?>
</div>
