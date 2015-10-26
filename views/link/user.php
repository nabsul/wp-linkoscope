<?php

use yii\widgets\ListView;
use yii\widgets\DetailView;

/** @var $this yii\web\View */
/** @var $user ShortCirquit\LinkoScopeApi\Models\UserProfile */

?>

<?= DetailView::widget(
    [
        'model'      => $user,
        'attributes' => ['name', 'url:url'],
    ]
); ?>

<h3>Posts by <?= $user->name ?>:</h3>

<?= ListView::widget(
    [
        'dataProvider' => $data,
        'itemView'     => '_linkItem',
        'options'      => ['class' => 'striped'],
    ]
); ?>
