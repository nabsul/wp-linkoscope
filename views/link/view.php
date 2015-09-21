<?php

use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\ActionColumn;
use yii\helpers\Url;

/** var $this yii\web\View */

?>

<?= DetailView::widget([
    'model' => $link,
    'attributes' => [
        'id', 'votes', 'title', 'url'
    ],
]); ?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $comments,
    'columns' => [
        'id', 'author', 'author_name', 'content.rendered',
        'actions' => [
            'class' => ActionColumn::className(),
            'template' => '{delete} {up} {down}',
            'urlCreator' => function($c, $m, $k, $i){
                return Url::to([$c, 'comment' => $m['id']]);
            },
            'buttons' => [
                'up' => function ($url, $model, $key) {
                    $options = array_merge([
                        'title' => Yii::t('yii', 'Up'),
                        'aria-label' => Yii::t('yii', 'Up'),
                        'data-pjax' => '0',
                    ]);
                    return Html::a('<span class="glyphicon glyphicon-arrow-up"></span>', $url, $options);
                },
                'down' => function ($url, $model, $key) {
                    $options = array_merge([
                        'title' => Yii::t('yii', 'Dn'),
                        'aria-label' => Yii::t('yii', 'Dn'),
                        'data-pjax' => '0',
                    ]);
                    return Html::a('<span class="glyphicon glyphicon-arrow-down"></span>', $url, $options);
                },
            ],
        ]
    ]
]); ?>

<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

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
