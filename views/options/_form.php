<?php

use app\models\Questions;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Options */
/* @var $form yii\widgets\ActiveForm */

$questions = ArrayHelper::map(Questions::find()->orderBy(['id'=>SORT_ASC])->all(), 'id', 'question');

?>

<div class="options-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-12">
            <?= $form->field($model, 'question_id')->dropDownList($questions, ['class'=>'form-control input-lg']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'option_id')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'name')->textInput([]) ?>
        </div>
    </div>


    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-lg">Salvar</button>
    </div>

    <?php ActiveForm::end(); ?>

</div>
