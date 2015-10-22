<?php

use yii\widgets\ListView;
use yii\widgets\DetailView;

/** @var $this yii\web\View */
/** @var $user ShortCirquit\LinkoScope\Models\UserProfile */

?>

<?= DetailView::widget([
    'model' => $user,
    'attributes' => [ 'id', 'name', 'username', 'url:url'],
]); ?>

<h3>Posts by <?= $user->username ?>:</h3>

<?= ListView::widget([
    'dataProvider' => $data,
    'itemView' => '_linkItem',
    'options' => ['class' => 'striped'],
]); ?>
