<?php
//vcuse kartik\grid\GridView;
use yii\helpers\Html;
use yii\grid\GridView;

        $client = new SoapClient("http://203.157.103.30/nrefer/webservicejson.asmx?WSDL");
        $params = array(
            'user' => 'user',
            'pass' => 'pass',
            'tableName' => 'refer_result',
            'datefield' => 'd_update',
            'datevalue' => "2016-05-11", //date("Y-m-d"),
            'datafield' => 'hosp_source',
            'datavalue' => '12275');
        $result = $client->DynamicSelectDB($params)->DynamicSelectDBResult;
        //echo $result;

        $data_selectdb = json_decode($result);

        //print_r($data_selectdb);
        $dataProvider = new \yii\data\ArrayDataProvider([
            //'key' => 'hoscode',
            'allModels' => $data_selectdb,
            'pagination' => FALSE,
        ]);



?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
//    'columns' => [
//        'referid_source',
//        'hospcode',
//        'hosp_source',
//        'd_update',
        //'created_at:datetime',
        // ...
//    ],
]) ?>
