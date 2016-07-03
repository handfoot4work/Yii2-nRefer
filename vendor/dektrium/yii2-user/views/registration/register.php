<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;
use yii\helpers\Url;
//use yii\widgets\ActiveForm;
use yii\widgets\ActiveForm;
use kartik\widgets\Typeahead;

/**
 * @var yii\web\View              $this
 * @var dektrium\user\models\User $user
 * @var dektrium\user\Module      $module
 */

$this->title = Yii::t('user', 'Sign up');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id'                     => 'registration-form',
                    'enableAjaxValidation'   => true,
                    'enableClientValidation' => false,
                ]); ?>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'username') ?>
                    </div>
                    <div class="col-md-6">
                        <?php if ($module->enableGeneratingPassword == false): ?>
                            <?= $form->field($model, 'password')->passwordInput() ?>
                        <?php endif ?>
                    </div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'prename')->textInput(["placeholder"=>"นพ., นาย, นาง, นส."]) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'fname') ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'lname') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'position')->textInput(["placeholder"=>"นายแพทย์, นักวิชาการคอมพิวเตอร์"]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'position_level')->textInput(["placeholder"=>"ชำนาญการ, ชำนาญงาน"]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'tel_office')->textInput(["placeholder"=>"9-999-9999 ต่อ 9999"]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'tel_mobile') ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group field-register-form-hcode required">
                            <label for="register-form-hcode">หน่วยงาน</label>
                                <?php
                                echo Typeahead::widget([
                                    'name' => 'register-form[hcode]',
                                    'options' => ['placeholder' => 'select office ...', 'class'=>"form-control",'required'=>true],
                                    'pluginOptions' => ['highlight'=>true],
                                    'id'=>'register-form-hcode',
                                    'dataset' => [
                                        [
                                            'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                                            'display' => 'value',
                                            'remote' => [
                                                'url' => Url::to(['/admin/lib/hospitals?q=%QUERY']),
                                                'wildcard' => '%QUERY'
                                            ],
                                            'limit' => 20,
                                        ]
                                    ]
                                ]);
                                 ?>
                        <div class="help-block"></div>
                    </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($model, 'email') ?>
                    </div>
                </div>

                <?= $form->field($model, 'remark')->textArea(['rows'=>4]) ?>
                <div class="row">
                    <div class="col-md-2 pull-right">
                        <?= Html::submitButton(Yii::t('user', 'Sign up'), ['class' => 'btn btn-success btn-block']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <p class="text-center">
            <?= Html::a(Yii::t('user', 'Already registered? Sign in!'), ['/user/security/login']) ?>
        </p>
    </div>
</div>
