<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;

/** @var $this yii\web\View */
/** @var $linkForm app\models\LinkForm */

?>
<h3>Shared Links:</h3>

<?php if (!Yii::$app->user->isGuest) : ?>
    <?= Html::beginForm(['link/new'])?>

    <div class="row">
        <div class="col-sm-4">
            Url: <?= Html::activeTextInput($linkForm, 'url') ?>
            <?= Html::activeHiddenInput($linkForm, 'title') ?>
            <?= Html::submitButton('Add New', ['class' => 'btn-xs btn-primary btn-success', 'name' => 'login-button']) ?>
        </div>
    </div>
    <?= Html::endForm() ?>
<?php endif; ?>

<?= ListView::widget([
    'dataProvider' => $data,
    'itemView' => '_linkItem',
    'options' => ['class' => 'striped'],
]); ?>
