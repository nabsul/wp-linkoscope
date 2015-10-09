<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */

?>
<h1>Links Index</h1>

<?= ListView::widget([
    'dataProvider' => $data,
    'itemView' => '_linkItem',
    'options' => ['class' => 'striped'],
]); ?>

<?= Html::a('Add New', ['new'], ['class' => 'btn btn-default btn-success']); ?>
