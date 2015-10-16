<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */

?>
<h3>Shared Links:</h3>
<?php if (!Yii::$app->user->isGuest) : ?>
    <?= Html::a('Add New', ['new'], ['class' => 'btn btn-default btn-success']); ?>
<?php endif; ?>
<?= ListView::widget([
    'dataProvider' => $data,
    'itemView' => '_linkItem',
    'options' => ['class' => 'striped'],
]); ?>
