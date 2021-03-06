<?php
/* @var $this yii\web\View */

use app\models\Interviews;
use app\models\StatsUtil;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Stats';

// calculate
$list = $data['all'];
$total = StatsUtil::total($list);
if (!empty($list)) {
    arsort($list);
    $keys = array_keys($list);

    $freq_rel = StatsUtil::frecuenciasRelativas($list);
    $list_media = StatsUtil::media($list);
    $list_varianza = StatsUtil::varianza($list);
    $list_desv_tip = sqrt($list_varianza);

    $columns = [];
    foreach (Interviews::$sex_list as $k => $value) if (!empty($value)) $columns[$value] = $value;
    foreach (Interviews::$age_list as $k => $value) if (!empty($value)) $columns[$value] = $value;
    foreach (Interviews::$edu_list as $k => $value) if (!empty($value)) $columns[$value] = $value;

    $fullData = [];
    $medias = [];
    $varianza = [];
    $desv_est = [];
    foreach ($columns as $colKey => $col) {
        $fullData[$colKey] = [];
        foreach ($keys as $key) {
            $fullData[$colKey][] = isset($data[$colKey][$key]) ? $data[$colKey][$key] : 0;
        }
        $f_rel[$colKey] = StatsUtil::frecuenciasRelativas($data[$colKey]);
        $medias[$colKey] = StatsUtil::media($fullData[$colKey]);
        $varianza[$colKey] = StatsUtil::varianza($fullData[$colKey]);
        $desv_est[$colKey] = sqrt($varianza[$colKey]);
    }
}
?>
<style>
    .blank {border-top: 0 none!important;border-bottom: 0 none!important;}
    .number {
        text-align: center;
        vertical-align: bottom !important;
    }
</style>
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

<?php if (!empty($list)) { ?>
    <div class="rows">

        <table class="table">
            <thead>
            <tr>
                <th rowspan="2"><h3>Respuestas</h3></th>
                <th colspan="2" class="number">Frecuencias</th>
                <th class="blank">&nbsp;</th>
                <th colspan="2" class="number">Sexo</th>
                <th colspan="3" class="number">Edad</th>
                <th colspan="3" class="number">Educacion</th>
            </tr>
            <tr>
                <th class="number" style="width:7%">Absoluta</th>
                <th class="number" style="width:7%">Relativa</th>
                <th class="blank" style="width:2%">&nbsp;</th>
                <?php foreach ($columns as $k => $value) { ?>
                    <th class="number" style="width:7%"><?= $value ?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($keys as $key) { ?>
                <tr>
                    <td><?= $key ?></td>
                    <td class="number"><?=$list[$key]?></td>
                    <td class="number"><?=number_format($freq_rel[$key],2,'.','')?></td>
                    <td class="blank">&nbsp;</td>
                    <?php foreach ($columns as $k => $value) { ?>
                        <td class="number" style="padding-top: 2px;padding-bottom: 2px;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 40%"><?= isset($data[$k][$key]) ? $data[$k][$key] : '0' ?></td>
                                    <td style="width: 60%;font-size: 10pt;"><?= number_format(isset($f_rel[$k][$key]) ? $f_rel[$k][$key] : 0, 2, '.', '')?></td>
                                </tr>
                            </table>

                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <td><h3>Media</h3></td>
                <td class="number"><?= number_format($list_media, 2, '.', '') ?></td>
                <td>&nbsp;</td>
                <td class="blank">&nbsp;</td>
                <?php foreach ($columns as $k => $value) { ?>
                    <td class="number"><?= number_format($medias[$k], 2, '.', '') ?></td>
                <?php } ?>
            </tr>
            <tr>
                <td><h3>Varianza</h3></td>
                <td class="number"><?= number_format($list_varianza, 2, '.', '') ?></td>
                <td>&nbsp;</td>
                <td class="blank">&nbsp;</td>
                <?php foreach ($columns as $k => $value) { ?>
                    <td class="number"><?= number_format($varianza[$k], 2, '.', '') ?></td>
                <?php } ?>
            </tr>
            <tr>
                <td><h3>Desviacion Estandar</h3></td>
                <td class="number"><?= number_format($list_desv_tip, 2, '.', '') ?></td>
                <td>&nbsp;</td>
                <td class="blank">&nbsp;</td>
                <?php foreach ($columns as $k => $value) { ?>
                    <td class="number"><?= number_format($desv_est[$k], 2, '.', '') ?></td>
                <?php } ?>
            </tr>
            </tfoot>
        </table>
    </div>

    <div>

        <table class="table">
            <tr>
                <th><h3>Covarianza</h3></th>
                <?php foreach ($columns as $cKey => $cVal) { ?>
                    <th class="number"><?= $cVal ?></th>
                <?php } ?>
            </tr>
            <?php foreach ($columns as $rKey => $rVal) { ?>
                <tr>
                    <th><?= $rVal ?></th>
                    <?php foreach ($columns as $cKey => $cVal) {

                        $data1 = $fullData[$rKey];
                        $data2 = $fullData[$cKey];
                        $coVal = StatsUtil::covarianza($data1, $data2)

                        ?>
                        <td class="number" style="width: 10%;"><?= number_format($coVal, 2, '.', '') ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div>

        <table class="table">
            <tr>
                <th><h3>Correlacion de Pearson</h3></th>
                <?php foreach ($columns as $cKey => $cVal) if ($cKey != 'all') { ?>
                    <th class="number"><?= $cVal ?></th>
                <?php } ?>
            </tr>
            <?php foreach ($columns as $rKey => $rVal) if ($rKey != 'all') { ?>
                <tr>
                    <th><?= $rVal ?></th>
                    <?php foreach ($columns as $cKey => $cVal) if ($cKey != 'all') {

                        $data1 = $fullData[$rKey];
                        $dt1 = $desv_est[$rKey];
                        $data2 = $fullData[$cKey];
                        $dt2 = $desv_est[$cKey];
                        $corr = StatsUtil::covarianza($data1, $data2) / ($dt1 * $dt2)

                        ?>
                        <td class="number" style="width: 10%;"><?= number_format($corr, 2, '.', '') ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
    </div>

<?php }  else { ?>
    <div style="text-align: center;padding: 40px;font-size: 14pt;"> No hay respuestas para esta pregunta </div>
<?php } ?>

</div>
