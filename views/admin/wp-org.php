<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
?>
<h1>WP Org Api Setup</h1>

<?= Html::beginForm(); ?>

Blog URL: <?= Html::textInput('blogUrl'); ?>

Consumer Key: <?= Html::textInput('consumerKey'); ?>

Consumer Secret: <?= Html::textInput('consumerSecret'); ?>

<?= html::submitButton(); ?>

<?= Html::endForm(); ?>
