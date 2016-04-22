<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Interviews */

$this->title = 'Nueva Entrevista';
$this->params['breadcrumbs'][] = ['label' => 'Entrevistas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="interviews-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
