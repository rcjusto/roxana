<?php

use app\models\Countries;
use app\models\Questions;
use app\models\Interviews;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Interviews */
/* @var $form yii\bootstrap\ActiveForm */

$questions = ArrayHelper::map(Questions::find()->all(), 'id', 'question');
$countries = ArrayHelper::map(Countries::find()->all(), 'id', 'name');

if (isset($_REQUEST['question_id']) && !empty($_REQUEST['question_id']) && array_key_exists($_REQUEST['question_id'], $questions)) {
    $text = $questions[$_REQUEST['question_id']];
    $questions = [$_REQUEST['question_id'] => $text];
}

?>

<div class="interviews-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'id')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'sex')->dropDownList(Interviews::$sex_list) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'age')->dropDownList(Interviews::$age_list) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'education')->dropDownList(Interviews::$edu_list) ?>
        </div>

        <div class="col-lg-3">
            <?= $form->field($model, 'city')->textInput() ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'zone')->textInput() ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'from_capital')->dropDownList(['', '1' => 'Si', '2' => 'No']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'years_in_capital')->textInput() ?>
        </div>

        <div class="col-lg-4">
            <?= $form->field($model, 'occupation')->textInput() ?>
        </div>
        <div class="col-lg-4">
            <?= $form->field($model, 'visited')->textInput() ?>
        </div>
        <div class="col-lg-4">
            <?= $form->field($model, 'lived')->textInput() ?>
        </div>
    </div>

    <?php if (!$model->isNewRecord) { ?>
        <?php foreach ($questions as $qId => $qText) {
            $options = ArrayHelper::map(\app\models\Options::find()->where(['question_id' => $qId])->orderBy(['name' => SORT_ASC])->all(), 'option_id', 'name');

            ?>
            <div style="background-color: #f8f8f8;padding: 20px;margin-bottom: 20px;">
                <div class="row">
                    <div class="col-lg-11" style="font-size: 13pt;font-weight: bold;"><?= $qId . '. ' . $qText ?></div>
                    <div class="col-lg-1" style="text-align: right"><a href="#" onclick="$('#answer_det_<?= $qId ?>').toggle();return false;"><span class="glyphicon glyphicon-chevron-down"></span></a></div>
                </div>
                <div id="answer_det_<?= $qId ?>" style="<?php if (count($questions)>1) echo 'display: none;'?>">
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-lg-12">
                            <label>Respuesta Original</label>
                            <textarea name="answer[<?= $qId ?>]" class="form-control"><?= $model->getOriginalAnswer($qId) ?></textarea>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-lg-12">
                            <div><label>Respuestas seleccionadas para estad&iacute;sticas</label></div>
                            <?php foreach ($options as $option_id => $name) { ?>
                                <label class="col-lg-2" style="padding-left: 1px!important;">
                                    <?= Html::checkbox("options_" . $qId . "[]", $model->hasOption($qId, $option_id), ['value' => $option_id]) ?>
                                    <span style="font-weight: normal"><?= $name ?></span>
                                </label>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>

    <div class="form-group">
        <button type="submit" class="<?= $model->isNewRecord ? 'btn btn-success btn-lg' : 'btn btn-primary btn-lg' ?>">
            <span class="glyphicon glyphicon-floppy-disk"></span> Salvar
        </button>
        <button type="button" class="btn btn-default btn-lg" style="float: right"
                onclick="if (confirm('Eliminar?')) $('#formDelete').submit();"><span
                class="glyphicon glyphicon-trash"></span> Eliminar Entrevista
        </button>
    </div>

    <?php ActiveForm::end(); ?>
    <form id="formDelete" action="<?= \yii\helpers\Url::to(['delete', 'id' => $model->id]) ?>" method="post"></form>
</div>
