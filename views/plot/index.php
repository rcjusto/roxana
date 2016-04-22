<?php
use app\models\Themes;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $countries [] */
/* @var $questions [] */
/* @var $colors [] */
/* @var $country string */
/* @var $question string */
/* @var $template Themes */

$template_id = !is_null($template) ? $template->id : 0;

?>
<div style="width: 100%;padding: 10px;">
    <?php $form = ActiveForm::begin(['id'=>'formPlot']) ?>
    <?= Html::hiddenInput('template', '', ['id'=>'fld_template'])?>
    <div class="row">
        <div class="col-lg-9">
            <?= Html::dropDownList('question', $question, $questions, ['class' => 'form-control input-lg', 'onchange' => '$(this).closest("form").submit();']) ?>
        </div>
        <div class="col-lg-3">
            <?= Html::dropDownList('country', $country, $countries, ['class' => 'form-control input-lg', 'onchange' => '$(this).closest("form").submit();']) ?>
        </div>
    </div>
    <?php ActiveForm::end() ?>
</div>

<div id="blockColorManagement" style="background-color: #f8f8f8;padding: 10px 20px;margin-bottom: 10px;" data="<?= \yii\helpers\Url::to(['themes/index', 'template'=>$template_id]) ?>"></div>

<div style="position: relative">

    <div style="position: absolute; top:10px;left: 10px;">
        <a class="btn btn-primary" href="<?= \yii\helpers\Url::to(['pdf', 'question' => $question, 'country' => $country ,'template'=>$template_id]) ?>"><span class="glyphicon glyphicon-save"></span> PDF</a>
        <a class="btn btn-primary" target="_blank" href="<?= \yii\helpers\Url::to(['image', 'question' => $question, 'country' => $country,'template'=>$template_id]) ?>"><span class="glyphicon glyphicon-save"></span> PNG</a>
    </div>

    <div id="blockSvg" class="load-svg2" data-url="<?= \yii\helpers\Url::to(['svg', 'question' => $question, 'country' => $country,'template'=>$template_id]) ?>"><?= $svg ?></div>
</div>

<div class="modal fade" id="modalGradient" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width: 500px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Generar gradiente de color</h4>
            </div>
            <div class="modal-body">
                <p>Seleccione los colores para generar el gradiente:</p>
                <form id="formGradient" action="<?= \yii\helpers\Url::to(['themes/gradient'])?>">
                <input type="hidden" id="gradientdest">
                <div class="row">
                    <div class="col-lg-6">
                        <label>Color 1</label>
                        <input type="text" name="gradientcolor1" class="minicolor form-control"/>
                    </div>
                    <div class="col-lg-6">
                        <label>Color 2</label>
                        <input type="text" name="gradientcolor2" class="minicolor form-control"/>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button id="btnGenerateGradient" type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
