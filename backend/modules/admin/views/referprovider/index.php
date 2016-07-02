<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\admin\ReferProviderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Refer Providers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="refer-provider-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?php Pjax::begin(); ?>
<?php
$Columns =  [
            ['class' => 'yii\grid\SerialColumn'],
            //'id',
            'region',
            'prov',
            [
                'label'=>'Provider',
                'attribute'=>'provider',
                'value'=>function($data) use ($referName) {
                    return $referName[$data["provider"]] ;
                }
            ],
            [
                'label'=>'จังหวัด/เขต',
                'attribute'=>'usage_group',
                'value'=>function($data) use ($referName) {
                    return $data["usage_group"]=='R'? 'เขต':'จังหวัด' ;
                }
            ],
//            'usage_group',
            'api_key:ntext',
            // 'secret_key',
            // 'secret_default',
            // 'hashing',
            'responder',
            //'tel',
            'date_register',
            //'date_expire',
//            'lastkeychange',
            'lastlogin',
            // 'remark:ntext',
            // 'lastupdate',

            ['class' => 'yii\grid\ActionColumn'],
        ];

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'panel'=>[
        'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-file"></i> '.$this->title.'</h3>',
         'type'=>'success',
         'before'=>'',
    ],
    'responsive'=>false,
    'headerRowOptions'=>['class'=>'text-info'],
    'containerOptions'=>['style'=>'overflow: auto'],
    'headerRowOptions'=>['class'=>'kartik-sheet-style'],
    'filterRowOptions'=>['class'=>'kartik-sheet-style'],
    //'floatHeader'=>true,
    'resizableColumns'=>true,
    //'toggleData'=>false,
    'hover'=>true,
    //'pjax'=>true,
    //'showPageSummary'=>true,
    'toolbar'=> [
        ['content'=>
            Html::a(Yii::t('app', '<i class="glyphicon glyphicon-plus"></i>'), ['create'], ['class' => 'btn btn-success']).
            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['/admin/referprovider'], ['data-pjax'=>1, 'class'=>'btn btn-success', 'title'=>'List condition'])
        ],
        'options' => ['class' => 'btn-group-sm'],
        '{export}',
        '{toggleData}',
    ],
    'columns'=>$Columns
]);
echo '<small class="pull-right"> ข้อมูล ณ '.date("Y-m-d H:i:s").'</small><br>';

?>
<?php Pjax::end(); ?></div>
<?php
use dosamigos\google\maps\LatLng;
use dosamigos\google\maps\overlays\InfoWindow;
use dosamigos\google\maps\overlays\Marker;
use dosamigos\google\maps\Map;
$coord = new LatLng(['lat'=>13.777234,'lng'=>100.561981]);
$map = new Map([
    'center'=>$coord,
    'zoom'=>6,
    'width'=>'100%',
    'height'=>'600',
]);
foreach($referProvider as $prov=>$c){
    if ($c["lat"]+0==0 && $c["long"]==0){
        $c["lat"] = 13.777234;
        $c["long"] = 100.561981;
    }
    $coords = new LatLng(['lat'=>$c["lat"],'lng'=>$c["long"]]);
    $marker = new Marker(['position'=>$coords]);
    //$pinImage = new MarkerImage("http://www.googlemapsmarkers.com/v1/009900/");
    $marker->attachInfoWindow( new InfoWindow([
        'content'=>' <h4>จังหวัด: ['.$prov.'] '.$c["changwatname"].'</h4>'
                    .'<table class="table">'
                    .'<tr> <td>เขต</td> <td>'.$c["region"].'</td> </tr>'
                    .'<tr> <td>Provider</td> <td>'.$c["name"].' '.$c["owner"].'</td> </tr>'
                    .'<tr> <td>ผู้รับผิดชอบ</td> <td>'.$c["responder"].'</td> </tr>'
                    .'</table> '
        ])
    );
    $marker->icon = '/images/marker/'.$c["marker"] ;
    $map->addOverlay($marker);
}
?>
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="glyphicon glyphicon-signal"></i> แสดงการใช้โปรแกรมเพื่อจัดการข้อมูลส่งต่อ
        </h3>
    </div>
    <div class="panel-body">
        <?php echo $map->display(); ?>
    </div>
</div>

<script type="text/javascript">
function pinSymbol(color) {
    return {
        path: 'M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z M -2,-30 a 2,2 0 1,1 4,0 2,2 0 1,1 -4,0',
        fillColor: color,
        fillOpacity: 1,
        strokeColor: '#000',
        strokeWeight: 2,
        scale: 1,
    };
}
</script>
