<?php

use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;

/** var $this yii\web\View */
/** var $link ShortCirquit\LinkoScopeApi\Models\Link */

?>

<h1>Link Details </h1>

<?= DetailView::widget([
    'model' => $link,
    'attributes' => [
        'link' => ['label' => 'Link', 'format' => 'raw',
                   'value' => "$link->title (" . Html::a($link->url, $link->url, ['target' => '_blank']) . ")"],
        'author' => ['label' => 'Author', 'value' => "$link->authorName ($link->date)"],
        'datails' => [
            'label' => 'Details',
            'value' => "Comments: $link->comments | Votes: $link->votes | Score: $link->score"
        ],
    ],
]); ?>

<h2>Comments</h2>

<?= ListView::widget([
    'dataProvider' => $comments,
    'itemView' => '_commentItem',
    'options' => ['class' => 'striped'],
]); ?>


<div class="site-login">
    <?php $form = ActiveForm::begin([
        'id' => 'comment-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

    <?= $form->field($commentForm, 'comment') ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Comment', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
