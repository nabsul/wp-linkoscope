<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $api ShortCirquit\WordPressApi\OrgWpApi */

?>
<h1>Admin</h1>

<p>
    <?php if (isset(Yii::$app->linko->config)) : ?>
        Current config: <br /> <?= nl2br(print_r(Yii::$app->linko->config, true)) ?>
    <?php else: ?>
        Nothing configured. Select one of the options below to get started.
    <?php endif ?>
</p>

<p>
    Click to (re)configure your REST API: <br/>
    <?= Html::a('WP Org Setup', ['wp-org'], ['class' => 'btn btn-default btn-success']); ?>
    <?= Html::a('WP Com Setup', ['wp-com'], ['class' => 'btn btn-default btn-success']); ?>
</p>
