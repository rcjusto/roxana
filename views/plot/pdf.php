<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $countries [] */
/* @var $questions [] */
/* @var $country string */
/* @var $question string */

?>

<div>

    <div>
        <div style="font-size: 16px;font-weight: bold;"><?= $questions[$question] ?></div>
        <div style="font-size: 14px;">Pa&iacute;s: <?= $countries[$country] ?></div>
    </div>

    <div>
        <img src="<?= $imgfile?>">
    </div>

</div>


