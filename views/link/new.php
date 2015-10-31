<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\models\LinkForm;

/** @var $linkForm LinkForm */
?>

<?php $form = ActiveForm::begin() ?>
<?= $form->field($linkForm, 'url') ?>
<?= $form->field($linkForm, 'title') ?>

<?= $form->field($linkForm, 'tags')->checkboxList($linkForm->tags,[
    'class' => 'btn-toolbar',
    'data-toggle' => 'buttons',
    'item' => function($index, $label, $name, $checked, $value){
        $isChecked = $checked == 1 ? ' checked' : '';
        $active = $checked == 1 ? ' active' : '';
        return "<div class='btn btn-default $active'>" .
        "<input type='checkbox' name='$name' value='$value' autocomplete='off' $isChecked />$label".
        "</div>";
    }
]) ?>

<?= Html::submitButton('Submit', ['class' => 'btn btn-default btn-success']) ?>
<?php $form->end(); ?>
