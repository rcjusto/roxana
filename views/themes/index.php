<?php
/* @var $this yii\web\View */
/* @var $templates [] */
/* @var $template Themes */

use app\models\Themes;
use yii\helpers\Html;

$session = Yii::$app->session;

?>
<form id="formColors">

    <?php if ($session->has('show_colors') && $session->get('show_colors')=='1') { ?>

    <div class="row" style="padding-bottom: 10px;margin-bottom: 10px;border-bottom: 1px solid #cccccc;">
        <?php if (!is_null($template)) { ?>
            <div class="col-lg-5">
                <div class="input-group">
                    <?= Html::dropDownList('template', $template->id, $templates, ['class' => 'form-control', 'id' => 'selectedTheme']) ?>
                    <span class="input-group-btn">
                        <button type="button" id="update_template" data="<?= \yii\helpers\Url::to(['themes/update', 'id' => $template->id]) ?>" class="btn btn-primary">Actualizar</button>
                        <button type="button" id="delete_template" data="<?= \yii\helpers\Url::to(['themes/delete', 'id' => $template->id]) ?>" class="btn btn-danger">Eliminar</button>
                    </span>
                </div>
            </div>
        <?php } ?>
        <div class="col-lg-2">
            <button type="button" id="refresh_map" class="btn btn-primary">Refresh Map</button>
        </div>
        <div class="col-lg-4 ">
            <div class="input-group">
                <?= Html::input('text', 'template_name', '', ['class' => 'form-control','placeholder'=>'Nueva combinacion de colores']) ?>
                <span class="input-group-btn">
                    <button type="button" id="add_template" data="<?= \yii\helpers\Url::to(['themes/create']) ?>" class="btn btn-primary">Adicionar</button>
                </span>
            </div>
        </div>
        <div class="col-lg-1 " style="text-align: right">
            <a href="#" style="font-size: 14pt;" id="closeColors" data-url="<?= \yii\helpers\Url::to(['themes/colors'])?>"><span class="glyphicon glyphicon-remove"></span></a>
        </div>
    </div>

    <div class="row" style="text-align: right;">
        <div class="col-lg-3">
            <div>
                <span>Color de fondo</span>
                <?= Html::input('text', 'color_bkg', $template->color_bkg, ['class' => 'color form-control']) ?>
            </div>
            <div style="margin-top: 10px;">
                <span>Pa&iacute;s seleccionado</span>
                <?= Html::input('text', 'color_sel', $template->color_sel, ['class' => 'color form-control']) ?>
            </div>
        </div>
        <div class="col-lg-9">
            <div>
                <span>Colores</span>
                <?php for ($i = 0; $i < 8; $i++) { ?>
                    <?= Html::input('text', 'color_set1[]', $template->getColor1($i), ['class' => 'color form-control colors1']) ?>
                <?php } ?>
            </div>
            <div style="margin-top: 10px;">
                <a href="#" class="btn-colors" id="gradient1"><span class="glyphicon glyphicon-adjust"></span> gradiente</a>
                <?php for ($i = 8; $i < 16; $i++) { ?>
                    <?= Html::input('text', 'color_set1[]', $template->getColor1($i), ['class' => 'color form-control colors1']) ?>
                <?php } ?>
            </div>
        </div>
    </div>

    <?php } else { ?>

        <div class="row" style="">
            <?php if (!is_null($template)) { ?>
                <div class="col-lg-5">
                        <?= Html::dropDownList('template', $template->id, $templates, ['class' => 'form-control', 'id' => 'selectedTheme']) ?>
                </div>
            <?php } ?>

            <div class="col-lg-7 " style="text-align: right;float: right;">
                <a href="#" style="" id="showColors" data-url="<?= \yii\helpers\Url::to(['themes/colors'])?>">Modificar combinaciones de colores</a>
            </div>
        </div>

    <?php } ?>
</form>
<script>
    $(function () {

        $('input.color').minicolors({});
        $('input.minicolor').minicolors({});

        $('#selectedTheme').change(function () {
            $('#fld_template').val($(this).val());
            $('#formPlot').submit();
        });

        $('#gradient1').click(function(){
            $('#gradientdest').val(1);
            $('#modalGradient').modal('show');
            return false;
        });

        $('#gradient2').click(function(){
            $('#gradientdest').val(2);
            $('#modalGradient').modal('show');
            return false;
        });

    });
</script>
