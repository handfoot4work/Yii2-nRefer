<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\admin\ReferProviderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="refer-provider-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'prov') ?>

    <?= $form->field($model, 'region') ?>

    <?= $form->field($model, 'provider') ?>

    <?= $form->field($model, 'date_register') ?>

    <?php // echo $form->field($model, 'date_expire') ?>

    <?php // echo $form->field($model, 'usage_group') ?>

    <?php // echo $form->field($model, 'api_key') ?>

    <?php // echo $form->field($model, 'secret_key') ?>

    <?php // echo $form->field($model, 'secret_default') ?>

    <?php // echo $form->field($model, 'hashing') ?>

    <?php // echo $form->field($model, 'responder') ?>

    <?php // echo $form->field($model, 'tel') ?>

    <?php // echo $form->field($model, 'lastkeychange') ?>

    <?php // echo $form->field($model, 'lastlogin') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <?php // echo $form->field($model, 'lastupdate') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
