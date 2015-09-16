<?php

use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */

?>
<h1>Links Index</h1>

<?= GridView::widget([
    'dataProvider' => $data,
    'columns' => [
        'id',
        'votes',
        'title',
        'url',
        'summary',
        'buttons' => [
            'class' => ActionColumn::className(),
            'urlCreator' => function($c, $m, $k, $i){
                return Url::to([$c, 'id' => $m->id]);
            }
        ],
    ]
]); ?>

