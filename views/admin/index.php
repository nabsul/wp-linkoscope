<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $api ShortCirquit\WordPressApi\OrgWpApi */

?>
<h1>Admin</h1>

<p>
    <?php if (isset(Yii::$app->linko->config)) : ?>
        <p>
            Current config: <br/> <?= nl2br(print_r(Yii::$app->linko->config, true)) ?>
        </p>

        <p>
        <?php
        $tags = [];
        foreach ([1 => 'tag1', 2 => 'tag2'] as $k => $v)
            $tags[] = ['id' => $k, 'name' => $v];
        ?>

        <?= join(' ', array_map(function($t){
            $trash = Html::tag('span', '', ['class' => 'glyphicon glyphicon-trash']);
            $trash = Html::a($trash, ['delete-tag', 'id' => $t['id']],
                [
                    'data-method' => 'post',
                    'data-confirm' => 'Are you sure you want to delete this tag?',
                ]);
            return Html::button($t['name'] . $trash, ['class' => 'btn']);
        }, $tags))
        ?>
        </p>

        <?php $form = \yii\widgets\ActiveForm::begin(); ?>
            <?= $form->field($tagForm, 'name'); ?>
            <?= Html::submitButton('Add', ['class' => 'btn btn-primary']); ?>
        <?php $form->end(); ?>

    <?php else: ?>
        Nothing configured. Select one of the options below to get started.
    <?php endif ?>
</p>

<p>
    Click to (re)configure your REST API: <br/>
    <?= Html::a('WP Org Setup', ['wp-org'], ['class' => 'btn btn-default btn-success']); ?>
    <?= Html::a('WP Com Setup', ['wp-com'], ['class' => 'btn btn-default btn-success']); ?>
</p>
