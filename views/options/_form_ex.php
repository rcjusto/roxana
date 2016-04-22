<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Options */

$model = new \app\models\Options();
$model->question_id = $question_id;
?>

<div class="options-form well" >

    <h3 style="margin-top: 0;">Adicionar Respuesta</h3>

    <?php $form = ActiveForm::begin(['action'=>\yii\helpers\Url::to(['create'])]); ?>

    <?= Html::activeHiddenInput($model, 'question_id') ?>

    <div class="row">
        <div class="col-lg-5">
            <?= Html::activeTextInput($model, 'option_id', ['placeholder'=>utf8_encode('Código'),'class'=>'form-control']) ?>
        </div>
        <div class="col-lg-5">
            <?= Html::activeTextInput($model, 'name', ['placeholder'=>'Respuesta','class'=>'form-control']) ?>
        </div>
        <div class="col-lg-2">
            <button type="submit" class="btn btn-primary ">Salvar</button>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
