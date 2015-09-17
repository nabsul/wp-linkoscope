<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

?>
<h1>Admin</h1>

<p>
    Nothing configured. Select one of the options below to get started.
</p>

<?php if (Yii::$app->session->hasFlash('error')) : ?>
<p style="color:red;">
    Error: <?= Yii::$app->session->getFlash('error'); ?>
</p>
<?php endif ?>

<p>
    <?= Html::a('WP Org Setup', ['wp-org'], ['class' => 'btn btn-default btn-success']); ?>
    <?= Html::a('WP Com Setup', ['wp-com'], ['class' => 'btn btn-default btn-success']); ?>
</p>
