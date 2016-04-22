<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Options */

$this->title = 'Modificar respuesta';
$this->params['breadcrumbs'][] = ['label' => 'Respuestas posibles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="options-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
