<?php
use yii\helpers\Html;
use kartik\grid\GridView;

$this->title = 'Web Service Logging summary';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = date("Y-m-d H:i:s");
//Provider	Province	Reg	No Event

$Columns = [
    [
        'class' => '\kartik\grid\SerialColumn'
    ],
    [
        'label'=>'Provider',
        'attribute' => 'provider',
        'headerOptions' => ['style'=>'text-align:center'],
        'contentOptions' => ['style'=>'text-align:left'],
    ],
    [
        'label'=>'Province',
        'attribute' => 'province',
        'headerOptions' => ['style'=>'text-align:center'],
        'contentOptions' => ['style'=>'text-align:center'],
    ],
    [
        'label'=>'Region',
        'attribute' => 'reg',
        'headerOptions' => ['style'=>'text-align:center'],
        'contentOptions' => ['style'=>'text-align:center'],
    ],
    [
        'label'=>'no. event',
        'value' => 'no_event',
        'format'=>['decimal',0],
        'headerOptions' => ['style'=>'text-align:right'],
        'contentOptions' => ['style'=>'text-align:right'],
    ],
];

echo GridView::widget([
    'dataProvider'=>$dataProvider,
    'hover'=>true,
    'responsive'=>true,
    'pjax'=>true,
    'pjaxSettings'=>[
        'neverTimeout'=>true,
    ],
    'resizableColumns'=>true,
    'columns'=>$Columns,
    'panel' => [
        'heading'=>'<h3 class="panel-title"><i class="fa fa-bars"></i> '.Html::encode($this->title).'</h3>',
        'type'=>'success',
        'before'=>'',
    ],
    'toolbar' => [
        ['content'=>
            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['/report/log','date'=>$date], ['data-pjax'=>1, 'class'=>'btn btn-primary', 'title'=>'List condition'])
        ],
        'options' => ['class' => 'btn-group-sm'],
        '{export}',
        '{toggleData}'
    ],
]);

?>

<p class="alert alert-danger"><small><u>หมายเหตุ</u></small> ระบบเก็บ Log เริ่มวันที่ 9 กันยายน 2559</p>
