<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/** @var $this yii\web\View */
/** @var $linkForm app\models\LinkForm */

/* @var $api ShortCirquit\LinkoScopeApi\iLinkoScope */
$api = Yii::$app->linko->getApi();
$tags = $api->listTags();

?>
<h3>Shared Links:</h3>

<?php if ($tags != null && count($tags) > 0) : ?>

    <p>
    Tags:
<?php
    $baseUrl = [''] + Yii::$app->request->get();
    unset($baseUrl['page']);

    foreach ($tags as $id => $name){
        echo Html::a(Html::button($name, ['class' => 'btn btn-primary']), $baseUrl + ['tag' => $id]);
    }
?>
    </p>
<?php endif; ?>

<?php if (!Yii::$app->user->isGuest) : ?>
    <?= Html::beginForm(['link/new']) ?>

    <div class="row">
        <div class="col-sm-4">
            Url: <?= Html::activeTextInput($linkForm, 'url') ?>
            <?= Html::activeHiddenInput($linkForm, 'title') ?>
            <?= Html::submitButton(
                'Add New', ['class' => 'btn-xs btn-primary btn-success', 'name' => 'login-button']
            ) ?>
        </div>
    </div>
    <?= Html::endForm() ?>
<?php endif; ?>

<?= ListView::widget(
    [
        'dataProvider' => $data,
        'itemView'     => '_linkItem',
        'options'      => ['class' => 'striped'],
    ]
); ?>
