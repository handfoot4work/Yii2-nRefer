<?php
if ($date1==date("Y-m-d")){
    echo '<meta http-equiv="refresh" content="90">';
}
?>

<?php
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use miloschuman\highcharts\Highcharts;

$this->title = 'จำนวนการส่งข้อมูล';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = 'แยกรายโรงพยาบาล';
$this->params['breadcrumbs'][] = ['label'=>'ข้อมูลวันที่ '.$date1.' -  '.$date2,'url'=>'/refer/monitor/table?date1='.$date1.'&date2='.$date2];
$this->params['breadcrumbs'][] = 'process '.date("H:i:s");
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'panel' => [
        'heading'=>'<h2 class="panel-title"><a href="'.Url::to(['/refer/report/sum_prov','region'=>$region,'date1'=>$date1,'date2'=>$date2]).'"><i class="glyphicon glyphicon-circle-arrow-left"></i></a> ข้อมูลจากตาราง '.$tablename.' </h2>',
        'type' => GridView::TYPE_DEFAULT,
        'before'=>'<form action="'.Url::to(['/refer/report/sum_hcode','region'=>$region]).'" method="post"><div class="col-md-2">'
            . ' <input type="date" class="form-control" name="date1" value="'.$date1.'">'
            . ' </div><div class="col-md-2">'
            . ' <input type="date" class="form-control" name="date2" value="'.$date2.'">'
            . ' </div><div class="col-md-1">'
            . '<button type="submit" class="form-control"><i class="glyphicon glyphicon-search"></i></button>'
            . '</div></form>',
    ],
    'toolbar'=> [
        ['content'=>
            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['/refer/report/sum_hcode','region'=>$region,'changwat'=>$changwat,'date1'=>$date1,'date2'=>$date2], ['data-pjax'=>1, 'class'=>'btn btn-primary', 'title'=>'Refresh Grid'])
        ],
        'options' => ['class' => 'btn-group-sm'],
        '{export}',
    ],
    'pjax'=>false,
    'hover'=>true,
    'striped'=>false,
    'headerRowOptions'=>['class'=>'warning'],
    'showPageSummary'=>true,
    'columns'=>[
        [
            'label'=>'จังหวัด',
            'value'=>function($data) { return $data["provcode"].' จังหวัด'.$data["changwatname"] ; },
            'width'=>'60px',
            'group'=>true,
            'groupedRow'=>true,
//            'groupOddCssClass'=>'kv-grouped-row',  // configure odd group cell css class
//            'groupEvenCssClass'=>'kv-grouped-row', // configure even group cell css class
//            'headerOptions' => ['style'=>'text-align:left'],
            'headerOptions' => ['style'=>'text-align:center'],
            'contentOptions' => ['style'=>'text-align:left;'],
            'contentOptions' => ['style'=>'text-align:left; background-color:lightgrey; ','class'=>'success'],
        ],
        [
            'label'=>'HCode',
            'value'=>'hcode',
            'width'=>'80px',
            'headerOptions' => ['style'=>'text-align:center'],
            'contentOptions' => ['style'=>'text-align:center;'],
            'pageSummary'=>'รวม',
        ],
        [
            'label'=>'ชื่อสถานพยาบาล',
            'format'=>'raw',
            'value'=> function($data) use ($date1,$date2){
                return Html::a($data['hname'],['/refer/report/table','region'=>$data["region"],'changwat'=>$data["provcode"],'hospcode'=>$data['hcode'],'date1'=>$date1,'date2'=>$date2] );
            },
        ],
        [
            'label'=>'OPD',
            'value'=>'opd',
            'format' => ['decimal',0],
            'width'=>'80px',
            'headerOptions' => ['style'=>'text-align:right'],
            'contentOptions' => ['style'=>'text-align:right;'],
            'pageSummary'=>true,
            'pageSummaryOptions'=>['class'=>'text-right text-info'],
        ],
        [
            'label'=>'IPD',
            'value'=>'ipd',
            'format' => ['decimal',0],
            'width'=>'80px',
            'headerOptions' => ['style'=>'text-align:right'],
            'contentOptions' => ['style'=>'text-align:right;'],
            'pageSummary'=>true,
            'pageSummaryOptions'=>['class'=>'text-right text-info'],
        ],
        [
            'label'=>'รวม',
            'value'=>'cases',
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
            'value'=>'inprov',
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
            'value'=>'inregion',
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
            'pageSummary'=>'',
            'pageSummaryOptions'=>['class'=>'text-right text-info'],
        ],
    ],
]);

$xAxis = [];
$graph = [];
foreach ($rawData as $value) {
    $xAxis[] = $value["hname"];
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
          'title' => ['text' => 'สถานพยาบาล'],
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
          'title' => ['text' => 'สถานพยาบาล'],
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
