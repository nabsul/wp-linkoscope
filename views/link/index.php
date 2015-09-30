<?php

use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\helpers\Url;
use automattic\LinkoScope\Models\Link;

/* @var $this yii\web\View */

?>
<h1>Links Index</h1>

<?= GridView::widget([
    'dataProvider' => $data,
    'columns' => [
        'id',
        'authorId',
        'date',
        'votes',
        'voteList' => ['label' => 'VoteList', 'value' => function(Link $link){return json_encode($link->voteList);}],
        'score',
        'title',
        'url',
        'actions' => [
            'class' => ActionColumn::className(),
            'template' => '{view} {delete} {up} {down}',
            'urlCreator' => function($c, $m, $k, $i){
                return Url::to([$c, 'id' => $m->id]);
            },
            'buttons' => [
                'up' => function ($url, $model, $key) {
                    $options = array_merge([
                        'title' => Yii::t('yii', 'Up'),
                        'aria-label' => Yii::t('yii', 'Up'),
                        'data-pjax' => '0',
                    ]);
                    return Html::a('<span class="glyphicon glyphicon-arrow-up"></span>', $url, $options);
                },
                'down' => function ($url, $model, $key) {
                    $options = array_merge([
                        'title' => Yii::t('yii', 'Dn'),
                        'aria-label' => Yii::t('yii', 'Dn'),
                        'data-pjax' => '0',
                    ]);
                    return Html::a('<span class="glyphicon glyphicon-arrow-down"></span>', $url, $options);
                },
            ],
        ],
    ]
]); ?>

<?= Html::a('Add New', ['new'], ['class' => 'btn btn-default btn-success']); ?>


