<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
?>
<h1>Admin</h1>

<p>
    Select one of the options below to configure the app's backend.
</p>

<p>
    <?= Html::a('WP Org Setup', ['wp-org'], ['class' => 'btn btn-default btn-success']); ?>
    <?= Html::a('WP Com Setup', ['wp-com'], ['class' => 'btn btn-default btn-success']); ?>
</p>
