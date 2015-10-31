<?php

use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $link ShortCirquit\LinkoScopeApi\Models\Link */
/* @var $comments ShortCirquit\LinkoScopeApi\Models\Comment[] */
/* @var $commentForm app\models\CommentForm */

?>

<h1>Link Details </h1>

<?= DetailView::widget(
    [
        'model'      => $link,
        'attributes' => [
            'link'    => [
                'label' => 'Link', 'format' => 'raw',
                'value' => "$link->title <br />" . Html::a($link->url, $link->url, ['target' => '_blank']),
            ],
            'datails' => [
                'label' => 'Details',
                'value' => "$link->authorName | " .
                    date('D, d M Y H:i:s', strtotime($link->date)) .
                    " | $link->comments comments | $link->votes votes",
            ],
            'tags' => [
                'label' => 'Tags',
                'format' => 'raw',
                'value' => join(' ', array_map(function ($i){
                    return Html::button($i, ['class' => 'btn btn-xs']);
                },$link->tags)),
            ],
        ],
    ]
); ?>

<h2>Comments</h2>
<?php if (!Yii::$app->user->isGuest) : ?>
    <?php $form = ActiveForm::begin(
        [
            'id'      => 'comment-form',
            'options' => ['class' => 'form-horizontal'],
        ]
    ); ?>
    <?= Html::activeTextInput($commentForm, 'comment', ['size' => 40]) ?>
    <?= Html::submitButton('Add Comment', ['class' => 'btn-xs btn-primary', 'name' => 'login-button']) ?>
    <?php ActiveForm::end(); ?>
<?php endif; ?>

<?= ListView::widget(
    [
        'dataProvider' => $comments,
        'itemView'     => '_commentItem',
        'options'      => ['class' => 'striped'],
    ]
); ?>
