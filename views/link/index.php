<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;

/** @var $this yii\web\View */
/** @var $linkForm app\models\LinkForm */

?>
<h3>Shared Links:</h3>

<?php if (!Yii::$app->user->isGuest) : ?>
    <?php $form = ActiveForm::begin([
        'id' => 'link-form',
    ]); ?>

    <div class="row">
        <div class="col-sm-4">
            <div class="col-sm-1">
                <?= Html::activeLabel($linkForm, 'title') ?>
            </div>
            <div class="col-sm-3">
                <?= Html::activeTextInput($linkForm, 'title') ?>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="col-sm-1">
                <?= Html::activeLabel($linkForm, 'url') ?>
            </div>
            <div class="col-sm-3">
                <?= Html::activeTextInput($linkForm, 'url') ?>
            </div>
        </div>
        <div class="col-sm-2">
            <?= Html::submitButton('Add New', ['class' => 'btn-xs btn-primary btn-success', 'name' => 'login-button']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
<?php endif; ?>

<?= ListView::widget([
    'dataProvider' => $data,
    'itemView' => '_linkItem',
    'options' => ['class' => 'striped'],
]); ?>
