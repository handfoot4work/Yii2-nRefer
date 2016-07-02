<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\admin\ReferProvider */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="refer-provider-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'prov')->dropdownList($changwatName, ['prompt' => '']); ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'region')->dropDownList([ '01' => '1', '02' => '2','03'=>'3','04'=>'4',
                    '05'=>'5','06'=>'6','07'=>'7','08'=>'8','09'=>'9','10'=>'10',
                    '11'=>'11','12'=>'12','13'=>'13' ], ['prompt' => '']) ?>
        </div>
        <div class="col-md-7">
            <?= $form->field($model, 'provider')->dropdownList($referName, ['prompt' => '']); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'usage_group')->dropDownList([ 'P' => 'จังหวัด', 'R' => 'เขต', ], ['prompt' => '']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'secret_key')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'secret_default')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'hashing')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'api_key')->textInput(['maxlength' => true, 'readonly'=>true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'responder')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'tel')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-2">
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'date_register')->textInput(['type'=>'date']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'date_expire')->textInput(['type'=>'date']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'lastkeychange')->textInput(['readonly'=>true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'lastlogin')->textInput(['readonly'=>true]) ?>
        </div>
    </div>

    <?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
