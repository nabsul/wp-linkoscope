<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\models\LinkForm;

/** @var $linkForm LinkForm */
?>

<?php $form = ActiveForm::begin() ?>
<?= $form->field($linkForm,'url') ?>
<?= $form->field($linkForm,'title') ?>
<?= Html::submitButton('Submit') ?>
<?php $form->end(); ?>
