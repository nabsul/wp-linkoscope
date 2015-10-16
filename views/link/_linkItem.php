<?php
use yii\helpers\Html;

/* @var $model ShortCirquit\LinkoScopeApi\models\Link */
?>

<div>
    <div>
        <?= ($index + 1) ?>:
<?php if (!Yii::$app->user->isGuest) : ?>
        <?= Html::a('<span class="glyphicon glyphicon-arrow-up"></span>', ['up', 'id' => $model->id,], ['title' => 'Up']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-arrow-down"></span>', ['down', 'id' => $model->id,], ['title' => 'Down']) ?>
<?php endif; ?>
        [<?= Html::a($model->title, $model->url, ['target' => '_blank',]); ?>]
        (<?= parse_url($model->url, PHP_URL_HOST) ?>)
    </div>
<?php if (!Yii::$app->user->isGuest) : ?>
    <div style="top: 0px; float: right;">
        <?= '' /* Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model->id,], ['title' => 'Delete']) */ ?>
    </div>
<?php endif; ?>
</div>
<div>
    <?=
        "$model->authorName | " .
        date('D d M Y', strtotime($model->date)) .
        " | $model->votes votes | " .
        Html::a($model->comments == 0 ? "discuss" : "$model->comments comments", ['link/view', 'id' => $model->id])
    ?>
</div>
