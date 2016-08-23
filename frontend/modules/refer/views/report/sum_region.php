<?php
if ($date2==date("Y-m-d")){
    echo '<meta http-equiv="refresh" content="300">';
}
?>
<?php
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use miloschuman\highcharts\Highcharts;
use kartik\widgets\ActiveForm;
use rmrevin\yii\fontawesome\FA;

$this->title = 'จำนวนการส่งข้อมูล';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = 'แยกรายเขต';
$this->params['breadcrumbs'][] = ['label'=>'ข้อมูลวันที่ '.$date1.' - '.$date2,'url'=>'/refer/monitor/table?date='.$date1];
$this->params['breadcrumbs'][] = 'process '.date("H:i:s");

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'panel' => [
        'heading'=>'<h2 class="panel-title"><i class="glyphicon glyphicon-user"></i> ข้อมูลจากตาราง '.$tablename.' </h2>',
        'type' => GridView::TYPE_DEFAULT,
        'before'=>'<form method="post"><div class="col-md-2">'
            . ' <input type="date" class="form-control" name="date1" value="'.$date1.'">'
            . ' </div><div class="col-md-2">'
            . ' <input type="date" class="form-control" name="date2" value="'.$date2.'">'
            . ' </div><div class="col-md-1">'
            . '<button type="submit" class="form-control"><i class="glyphicon glyphicon-search"></i></button>'
            . '</div></form>'
    ],
    'toolbar'=> [
        ['content'=>
            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['/refer/report/sum_region','date1'=>$date1,'date2'=>$date2], ['data-pjax'=>1, 'class'=>'btn btn-primary', 'title'=>'Refresh Grid'])
        ],
        'options' => ['class' => 'btn-group-sm'],
        '{export}',
        //'{toggleData}',
    ],
//    'responsive'=>false,
//    'hover'=>true,
    'pjax'=>false,
    'hover'=>true,
    'striped'=>false,
    'headerRowOptions'=>['class'=>'warning'],
    'showPageSummary'=>true,
    'columns'=>[
        [
            'label'=>'เขตสุขภาพ',
            'format'=>'raw',
            'value'=> function($data) use ($date1,$date2){
                return (
                    $data["cases"]>0?
                    Html::a($data['region'],['/refer/report/sum_prov','region'=>$data['region'],'date1'=>$date1,'date2'=>$date2] ):
                    $data['region']
                );
            },
            'width'=>'80px',
            'headerOptions' => ['style'=>'text-align:left'],
            'contentOptions' => ['style'=>'text-align:left;'],
            'pageSummary'=>'รวม',
        ],
        [
            'label'=>'OPD',
            'value'=> function($data){ return isset($data["opd"])? $data["opd"]:0;},
            'format' => ['decimal',0],
            'width'=>'80px',
            'headerOptions' => ['style'=>'text-align:right'],
            'contentOptions' => ['style'=>'text-align:right;'],
            'pageSummary'=>true,
            'pageSummaryOptions'=>['class'=>'text-right text-info'],
        ],
        [
            'label'=>'IPD',
            'value'=> function($data){ return isset($data["ipd"])? $data["ipd"]:0;},
            'format' => ['decimal',0],
            'width'=>'80px',
            'headerOptions' => ['style'=>'text-align:right'],
            'contentOptions' => ['style'=>'text-align:right;'],
            'pageSummary'=>true,
            'pageSummaryOptions'=>['class'=>'text-right text-info'],
        ],
        [
            'label'=>'รวม',
            'value'=> function($data){ return isset($data["cases"])? $data["cases"]:0;},
            'format' => ['decimal',0],
            'width'=>'80px',
            'hAlign'=>'right',
            'pageSummary'=>true,
            'headerOptions' => ['style'=>'text-align:right','class'=>'danger'],
            'contentOptions' => ['style'=>'text-align:right; ','class'=>'danger'],
            'pageSummaryOptions'=>['class'=>'text-right text-info danger'],
        ],
        [
            'label'=>'ส่งใน จ.',
            'value'=> function($data){ return isset($data["inprov"])? $data["inprov"]:0;},
            'format' => ['decimal',0],
            'width'=>'80px',
            'headerOptions' => ['style'=>'text-align:right'],
            'contentOptions' => ['style'=>'text-align:right;'],
            'pageSummary'=>true,
            'pageSummaryOptions'=>['class'=>'text-right text-info'],
        ],
        [
            'label'=>'ส่งนอก จ.',
            'value'=>function($data){ return $data["cases"]-$data["inprov"];},
            'format' => ['decimal',0],
            'width'=>'80px',
            'headerOptions' => ['style'=>'text-align:right'],
            'contentOptions' => ['style'=>'text-align:right;'],
            'pageSummary'=>true,
            'pageSummaryOptions'=>['class'=>'text-right text-info'],
        ],
        [
            'label'=>'ส่งใน เขต',
            'value'=> function($data){ return isset($data["inregion"])? $data["inregion"]:0;},
            'format' => ['decimal',0],
            'width'=>'80px',
            'headerOptions' => ['style'=>'text-align:right'],
            'contentOptions' => ['style'=>'text-align:right;','class'=>'info'],
            'pageSummary'=>true,
            'pageSummaryOptions'=>['class'=>'text-right text-info'],
        ],
        [
            'label'=>'ส่งนอก เขต',
            'value'=>function($data){ return $data["cases"]-$data["inregion"];},
            'format' => ['decimal',0],
            'width'=>'80px',
            'headerOptions' => ['style'=>'text-align:right'],
            'contentOptions' => ['style'=>'text-align:right;','class'=>'info'],
            'pageSummary'=>true,
            'pageSummaryOptions'=>['class'=>'text-right text-info'],
        ],
        [
            'label'=>'(%) นอกเขต',
            'value'=>function($data){ return ($data["cases"]-$data["inregion"])*100/$data["cases"] ;},
            'format' => ['decimal',2],
            'width'=>'80px',
            'headerOptions' => ['style'=>'text-align:right'],
            'contentOptions' => ['style'=>'text-align:right;','class'=>'info'],
            'pageSummary'=>function ($data) {
                return number_format($Cases["percent"],2);
            },
            'pageSummaryOptions'=>['class'=>'text-right text-info'],
        ],
    ],
]);

$xAxis = [];
$graph = [];
foreach ($rawData as $value) {
    $xAxis[] = $value["region"];
    $graph["opd"][] = $value["opd"]+0;
    $graph["ipd"][] = $value["ipd"]+0;
    $graph["cases"][] = $value["cases"]+0;
    $graph["inprov"][] = $value["inprov"]+0;
    $graph["inregion"][] = $value["inregion"]-$value["inprov"];
    $graph["outregion"][] = $value["cases"]-$value["inregion"];
}

$ChartColor = ["#3416ef","#17de00","#ef9000","#d41212"];

echo Highcharts::widget([
   'options' => [
      'title' => ['text' => $this->title.' จำแนกตามแผนกที่ส่ง'],
      'subtitle' => ['text' => 'ข้อมูลวันที่ '.$date1.' - '.$date2],
      'xAxis' => [
          'title' => ['text' => 'เขตสุขภาพ'],
         'categories' => $xAxis
      ],
      'yAxis' => [
         'title' => ['text' => 'ราย'],
         'min'=>0,
      ],
      'credits'=>['enabled'=>false],
      'colors'=>$ChartColor,
      'series' => [
         ['name' => 'OPD', 'type'=>'spline' , 'data' => $graph["opd"]],
         ['name' => 'IPD', 'type'=>'spline' , 'data' => $graph["ipd"]],
         ['name' => 'รวม', 'type'=>'column' , 'data' => $graph["cases"]]
      ]
   ]
]);

echo Highcharts::widget([
   'options' => [
      'title' => ['text' => $this->title.' จำแนกตามที่อยู่'],
      'subtitle' => ['text' => 'ข้อมูลวันที่ '.$date1.' - '.$date2],
      'xAxis' => [
          'title' => ['text' => 'เขตสุขภาพ'],
         'categories' => $xAxis
      ],
      'yAxis' => [
         'title' => ['text' => 'ราย'],
         'min'=>0,
      ],
      'credits'=>['enabled'=>false],
      'colors'=>$ChartColor,
        'tooltip'=>[
            'pointFormat'=> '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.2f}%)<br/>',
            'shared'=> true
        ],
      'plotOptions'=>[
            'column'=>[
                'stacking'=> 'percent',
                'dataLabels'=>[
                    'enabled'=>true,
                    //'color'=>'(Highcharts.theme && Highcharts.theme.dataLabelsColor) || "white"',
                    'style'=>[
                        'textShadow'=> '0 0 3px black'
                    ]
                ]
            ]
        ],
        'series' => [
         ['name' => 'ใน จ.', 'type'=>'column' , 'data' => $graph["inprov"]],
         ['name' => 'นอก จ.', 'type'=>'column' , 'data' => $graph["inregion"]],
         ['name' => 'นอกเขต', 'type'=>'column' , 'data' => $graph["outregion"]]
      ]
   ]
]);


?>
