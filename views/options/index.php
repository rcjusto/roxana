<?php

use app\models\Options;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $data Options[] */

$this->title = 'Respuestas Posibles';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="options-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin() ?>
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-lg-12">
            <?= Html::dropDownList('question',$question, $questions, ['class'=>'form-control input-lg', 'onchange'=>'$(this).closest("form").submit();']) ?>
        </div>
    </div>
    <?php ActiveForm::end() ?>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>C&oacute;digo</th>
            <th>Respuesta</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($data)) { ?>
            <tr>
                <td colspan="3">
                    <div style="padding: 20px;text-align: center;font-size: 12pt;">No hay respuestas definidas para esta pregunta</div>
                </td>
            </tr>
        <?php } ?>
        <?php foreach($data as $row) { ?>
            <tr>
                <td><?=$row->option_id?></td>
                <td><?=$row->name?></td>
                <td style="text-align: right">
                    <a href="<?=\yii\helpers\Url::to(['update','question_id'=>$row->question_id,'option_id'=>$row->option_id])?>"><span class="glyphicon glyphicon-pencil"></span></a>
                    &nbsp;
                    <a href="<?=\yii\helpers\Url::to(['delete','question_id'=>$row->question_id,'option_id'=>$row->option_id])?>" onclick="return confirm('Delete?');"><span class="glyphicon glyphicon-trash"></span></a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <div>
        <?= $this->render('_form_ex',['question_id'=>$question, 'questions'=>$questions])?>
    </div>

</div>
