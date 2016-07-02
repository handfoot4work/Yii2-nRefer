<?php
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\editable\Editable;

$this->title = 'แสดงข้อมูล';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = $tablename;
$this->params['breadcrumbs'][] = ['label'=>$date1.' - '.$date2,'url'=>'/refer/report/sum_hcode?date1='.$date1.'&date2='.$date2];

$PTypeDisease = ['01'=>'STEMI', '02'=> 'Stroke', '03'=>'trauma', '04'=>'cancer',
            '05'=>'sepsis', '06'=>'pregnancy,labor,postpartum', '07'=> 'new born', '99'=> 'อื่นๆ'];
$Emergency =['1'=> 'life threatening', '2'=>'emergency', '3'=> 'urgent', '4'=>'acute', '5'=>'non acute'];
$CauseOut = ['1' => 'เพื่อการวินิจฉัยและรักษา', '2' => 'เพื่อการวินิจฉัย', '3' => 'เพื่อการรักษาต่อเนื่อง', '4' => 'เพื่อการดูแลต่อใกล้บ้าน',
                '5' => 'ตามความต้องการผู้ป่วย', '6' => 'เพื่อส่งผู้ป่วยกลับไปยังสถานพยาบาลที่ส่งผู้ป่วยมา',
                '7' => 'เป็นการตอบกลับการส่งต่อ(ไม่ได้ส่งผู้ป่วย)'];
$PtType = ['1' => 'ผู้ป่วยทั่วไป', '2' => 'ผู้ป่วยอุบัติเหตุ', '3' => 'ผู้ป่วยฉุกเฉิน(ยกเว้นอุบัติเหตุ)'];
$EmergencyColor = ['1'=>'#ff0000', '2'=>'#ffb800', '3'=>'#ffe600', '4'=>'#42ff00', '5'=>'#efefef'];

/*echo '<label>Person Name</label><br>';
echo Editable::widget([
    'name'=>'hospname',
    'asPopover' => true,
    'value' => $rawData[0][hospname],
    'header' => 'Name',
    'size'=>'md',
    'options' => ['class'=>'form-control', 'placeholder'=>'Enter person name...']
]);
echo '<label>Province</label><br>';
echo Editable::widget([
    'name'=>'hname_destination',
    'asPopover' => true,
    'header' => 'Province',
    'format' => Editable::FORMAT_BUTTON,
    'inputType' => Editable::INPUT_DROPDOWN_LIST,
    'data'=>$rawData, // any list of values
    'options' => ['class'=>'form-control', 'prompt'=>'Select province...'],
    'editableValueOptions'=>['class'=>'text-danger']
]);
echo '<br /><br />';*/

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'panel' => [
        'heading'=>'<h2 class="panel-title"><a href="'.Url::to(['/refer/report/sum_hcode','region'=>$region,'changwat'=>$changwat,'date1'=>$date2,'date1'=>$date2]).
                '"><i class="glyphicon glyphicon-circle-arrow-left"></i></a> ข้อมูลจากตาราง '.$tablename.' </h2>',
        'type' => GridView::TYPE_DEFAULT,
        'before'=>'<form method="post"><div class="col-md-2">'
            . ' <input type="date" class="form-control" name="date1" value="'.$date1.'">'
            . ' </div><div class="col-md-2">'
            . ' <input type="date" class="form-control" name="date2" value="'.$date2.'">'
            . ' </div><div class="col-md-1">'
            . '<button type="submit" class="form-control"><i class="glyphicon glyphicon-search"></i></button>'
            . '</div></form>',
    ],
    'toolbar'=> [
        ['content'=>
            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['/refer/monitor/table','region'=>$region,'changwat'=>$changwat,'hospcode'=>$hospcode,'date1'=>$date1,'date2'=>$date2], ['data-pjax'=>1, 'class'=>'btn btn-primary', 'title'=>'Refresh Grid'])
        ],
        'options' => ['class' => 'btn-group-sm'],
        '{export}',
    ],
    'pjax'=>true,
    'hover'=>true,
    'striped'=>false,
    'containerOptions' => ['style'=>'overflow: auto'],
    'headerRowOptions'=>['class'=>'warning'],
    'columns'=>[
        [
            'label'=>'สถานพยาบาล',
            'value'=>function($data) { return $data["HOSPCODE"].' '.$data["hospname"] ; },
            'width'=>'60px',
            'group'=>true,
            'groupedRow'=>true,
            'headerOptions' => ['style'=>'text-align:center'],
            'contentOptions' => ['style'=>'text-align:left;'],
        ],
        [
            'label'=>'วันที่',
            'value'=> 'DATETIME_REFER',
            'headerOptions' => ['style'=>'text-align:center'],
            'contentOptions' => ['style'=>'text-align:left;'],
        ],
        [
            'label'=>'ส่งไป',
            'value'=> function ($data) { return $data['hname_destination']==""? $data["HOSP_DESTINATION"]:($data['hname_destination'].' จ.'.$data['changwatname']); },
            'headerOptions' => ['style'=>'text-align:center'],
        ],
/*        [
            'label'=>'HN',
            'value'=> 'PID',
            'headerOptions' => ['style'=>'text-align:center'],
        ],*/
        [
            'label'=>'VisitNo',
            'value'=> 'SEQ',
            'headerOptions' => ['style'=>'text-align:center'],
        ],
        [
            'label'=>'AN',
            'value'=> 'AN',
            'headerOptions' => ['style'=>'text-align:center'],
        ],
        [
            'label'=>'เลขที่ Refer',
            'value'=> 'REFERID',
            'headerOptions' => ['style'=>'text-align:center'],
        ],
        [
            'label'=>'Emergency',
            'format'=>'raw',
            'value'=> function($data) use ($Emergency,$EmergencyColor) { return
                '<span class="badge" style="background-color: '.$EmergencyColor[$data['EMERGENCY']].'">'
                . ' &nbsp; </span> '
                . ($Emergency[$data['EMERGENCY']]==""? $data['EMERGENCY']:$Emergency[$data['EMERGENCY']]);},
            'headerOptions' => ['style'=>'text-align:center'],
            'contentOptions' => ['style'=>'text-align:left;'],
        ],
        [
            'label'=>'สาเหตุ',
            'value'=> function($data) use ($CauseOut) { return $CauseOut[$data['CAUSEOUT']];},
            'headerOptions' => ['style'=>'text-align:center'],
            'contentOptions' => ['style'=>'text-align:left;'],
        ],
        [
            'label'=>'Pt Type',
            'value'=> function($data) use ($PtType) { return $PtType[$data['PTYPE']];},
            'headerOptions' => ['style'=>'text-align:center'],
            'contentOptions' => ['style'=>'text-align:left;'],
        ],
        [
            'label'=>'Disease',
            'value'=>'PTYPEDISC',
            'headerOptions' => ['style'=>'text-align:center'],
            'contentOptions' => ['style'=>'text-align:center;'],
        ],
        [
            'label'=>'เลขที่ Refer จ.',
            'value'=> 'REFERID_PROVINCE',
            'headerOptions' => ['style'=>'text-align:center'],
            'contentOptions' => ['style'=>'text-align:center;'],
        ],
    ],
]);

?>
