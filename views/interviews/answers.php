<?php

use app\models\Options;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
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
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];

$options = ArrayHelper::map(Options::find()->where(['question_id' => $question])->orderBy(['name' => SORT_ASC])->all(), 'option_id', 'name');


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

            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ETIQUETADO</th>
                    <th>RESPUESTA ORIGINAL</th>
                    <th>SELECCIONADA PARA ESTAD&Iacute;STICAS</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data as $row) { ?>
                    <tr>
                        <td nowrap="nowrap" style="width: 10%"><a target="_blank" href="<?= \yii\helpers\Url::to(['update', 'id' => $row->id]) ?>"><?= $row->id ?></a></td>
                        <td style="width: 45%"><?= $row->getOriginalAnswer($question) ?></td>
                        <td style="width: 45%;padding-right: 0!important;">
                            <?= Html::listBox('test[]', $row->getActualAnswersIds($question), $options,
                                [
                                    'class' => 'form-control select2 update-answer',
                                    'multiple' => 'multiple',
                                    'data-update' => \yii\helpers\Url::to(['update-answer', 'question_id'=>$question, 'id'=>$row->id]),
                                ]
                            ) ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

</div>
