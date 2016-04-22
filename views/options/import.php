<?php

use app\models\Options;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Import';
$this->params['breadcrumbs'][] = $this->title;

$items = ArrayHelper::map(Options::find()->where(['question_id' => $question_id])->orderBy(['name' => SORT_ASC])->all(), 'option_id', 'name');

?>
<div class="options-index">

    <h1>Import</h1>
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <table class="table">
        <?php foreach ($data as $cad) { ?>
            <tr>
                <td><?= Html::dropDownList("relation[$cad]", '', $items, ['class' => 'form-control', 'prompt'=>'']) ?></td>
                <td><?= $cad ?></td>
            </tr>
        <?php } ?>
    </table>
    <button>Submit</button>

    <?php ActiveForm::end() ?>
</div>
