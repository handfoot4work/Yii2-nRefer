<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\admin\ReferProvider */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Refer Provider',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Refer Providers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="refer-provider-update panel panel-primary">
    <div class="panel-heading"><h5><?= Html::encode($this->title) ?></h5></div>
    <div class="panel-body">
        <?= $this->render('_form', [
            'model' => $model,
            'changwatName'=>$changwatName,
            'referName'=>$referName
        ]) ?>

    </div>
</div>
