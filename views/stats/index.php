<?php
/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Stats';

?>
<div class="interviews-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin() ?>
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-lg-9">
            <?= Html::dropDownList('question', $question, $questions, ['class' => 'form-control input-lg', 'onchange' => '$(this).closest("form").submit();']) ?>
        </div>
        <div class="col-lg-3">
            <?= Html::dropDownList('country', $country, $countries, ['class' => 'form-control input-lg', 'onchange' => '$(this).closest("form").submit();']) ?>
        </div>
    </div>
    <?php ActiveForm::end() ?>


</div>
