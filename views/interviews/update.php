<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Interviews */

$this->title = 'Actualizar Entrevista: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Entrevistas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="interviews-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
