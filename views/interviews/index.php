<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $countries [] */
/* @var $questions [] */
/* @var $country string */
/* @var $question string */
/* @var $data \app\models\Interviews[] */

$this->title = 'Entrevistas';
?>
<div class="interviews-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin() ?>
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-lg-9">
            <?= Html::dropDownList('question',$question, $questions, ['class'=>'form-control input-lg', 'onchange'=>'$(this).closest("form").submit();']) ?>
        </div>
        <div class="col-lg-3">
            <?= Html::dropDownList('country',$country, $countries, ['class'=>'form-control input-lg', 'onchange'=>'$(this).closest("form").submit();']) ?>
        </div>
    </div>
    <?php ActiveForm::end() ?>

    <div class="row">
        <div class="col-lg-12">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ETIQUETADO</th>
                    <th>SEXO</th>
                    <th>EDAD</th>
                    <th>INSTRUCCI&Oacute;N</th>
                    <th>RESPUESTA ORIGINAL</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($data as $row) { ?>
                    <tr>
                        <td nowrap="nowrap"><?=$row->id?></td>
                        <td nowrap="nowrap"><?=$row->sex?></td>
                        <td nowrap="nowrap"><?=$row->age?></td>
                        <td nowrap="nowrap"><?=$row->getEducationDesc()?></td>
                        <td><?=$row->getOriginalAnswer($question)?></td>
                        <td><a href="<?= \yii\helpers\Url::to(['update','question_id'=>$question, 'id'=>$row->id])?>"><span class="glyphicon glyphicon-pencil"></span></a></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>


<div class="row">
    <div class="col-lg-3">
        <?= Html::a('Addicionar', ['create'], ['class' => 'btn btn-success']) ?>
    </div>
    <div class="col-lg-9" style="text-align: right">
        <?= Html::a(utf8_encode('Comparar respuesta original con seleccionada para estadísticas'), ['answers'], []) ?>
    </div>
</div>

</div>
