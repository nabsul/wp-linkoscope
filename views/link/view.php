<?php

use yii\widgets\DetailView;

/** var $this yii\web\View */

?>

<?= DetailView::widget([
    'model' => $link,
    'attributes' => [
        'id', 'votes', 'title', 'url'
    ],
]); ?>
