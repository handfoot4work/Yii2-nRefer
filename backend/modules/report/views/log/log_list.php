<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\ActiveForm;

$this->title = 'Web Service Logging';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = date("Y-m-d H:i:s");

$Columns = [
    [
        'class' => '\kartik\grid\SerialColumn'
    ],
    [
        'label'=>'ref',
        'format' => 'raw',
        'value' => function ($data) {
            return Html::a($data["ref"],['/report/log/log-detail','id'=>$data["ref"]],['class'=>'btn btn-default btn-xs'] );
        },
        'headerOptions' => ['style'=>'text-align:center'],
        'contentOptions' => ['style'=>'text-align:left'],
    ],
    'type',
    [
        'label'=>'วันที่',
        'attribute'=>'date',
        'headerOptions' => ['style'=>'text-align:center'],
        'contentOptions' => ['style'=>'text-align:left'],
    ],
    [
        'label'=>'apikey',
        'attribute'=>'apikey',
        'headerOptions' => ['style'=>'text-align:center'],
        'contentOptions' => ['style'=>'text-align:left'],
    ],
    [
        'label'=>'token',
        'attribute'=>'token',
        'headerOptions' => ['style'=>'text-align:center'],
        'contentOptions' => ['style'=>'text-align:left'],
    ],
    [
        'label'=>'Providername',
        'attribute'=>'providername',
        'headerOptions' => ['style'=>'text-align:center'],
        'contentOptions' => ['style'=>'text-align:left'],
    ],
    [
        'label'=>'จ.',
        'format' => 'raw',
        'value' => function ($data) {
                     return $data["prov"]==""? $data["prov2"]:$data["prov"];
                 },
        'headerOptions' => ['style'=>'text-align:center'],
        'contentOptions' => ['style'=>'text-align:left'],
    ],
    [
        'label'=>'เขต',
        'format' => 'raw',
        'value' => function ($data) {
                     return $data["region"]==""? $data["region2"]:$data["region"];
                 },
        'headerOptions' => ['style'=>'text-align:center'],
        'contentOptions' => ['style'=>'text-align:left'],
    ],
    [
        'label'=>'group',
        'format' => 'raw',
        'value' => function ($data) {
                     return $data["usage_group"]==""? $data["usage_group2"]:$data["usage_group"];
                 },
        'headerOptions' => ['style'=>'text-align:center'],
        'contentOptions' => ['style'=>'text-align:left'],
    ],
    'flag'
    //    Ref	Date	Token	Apikey	Provider Name	Type	Providername	Refno	Event	Flag	Clientdetail	Lastupdate	Prov	Region	Usage Group	Prov2	Region2	Usage Group2
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
            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['/report/log/list','date'=>$date], ['data-pjax'=>1, 'class'=>'btn btn-primary', 'title'=>'List condition'])
        ],
        '{export}',
        '{toggleData}'
    ],
]);
?>
<p class="alert alert-danger"><small><u>หมายเหตุ</u></small> ระบบเก็บ Log เริ่มวันที่ 9 กันยายน 2559</p>
