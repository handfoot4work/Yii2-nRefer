<?php
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Web Service Logging';
$this->params['breadcrumbs'][] = ['label'=>'Pharmacy', 'url'=>'/pharmacy'];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = $rawData["ref"];

if ($rawData["type"]==1){
    $Events = (array) json_decode(base64_decode($rawData["event"]));
    $Event = '';
    foreach ($Events as $key => $value) {
        if ($key!="secret-key"){
            if (isset($Events["base64"]) && $key=="data"){
                if ($Events["base64"]==true){
                    $Datas = (array) json_decode(base64_decode($value));
                } else {
                    $Datas = (array) json_decode($value);
                }
                //$Event .= $key.':'.nl2br(json_encode($Datas)).'<br>';
                $Event .= '<table class="table">';
                foreach ($Datas as $fld => $data) {
                    if (is_array($data)){
                        $Event .= '<tr><td>&nbsp; &nbsp; </td><td>'.$fld.':</td><td><table class="table">';
                        foreach ($data as $fld2 => $value2) {
                            foreach ($value2 as $fld3 => $value3) {
                                $Event .= '<tr><td>&nbsp; &nbsp; </td><td>'.$fld3.'</td><td>'.$value3.'</td></tr>';
                            }
                        }
                        $Event .= '</table></td></tr>';
                    } else {
                        $Event .= '<tr><td>&nbsp; &nbsp; </td><td>'.$fld.'</td><td>'.$data.'</td></tr>';
                    }
                }
                $Event .= $key.':</table>';
            } else {
                $Event .= $key.' : '.$value.'<br>';
            }
        }
    }
} else {
    $Event = $rawData["event"];
}
?>

<div class="panel panel-info">
    <div class="panel-heading">
        <?=Html::a('<i class="fa fa-chevron-circle-left"></i>',['/report/log'])?> Log detail
    </div>
    <div class="panel-body">
        <table class="table">
            <tr><td>ref </td><td><strong><?=$rawData["ref"]?></strong></td></tr>
            <tr><td>date </td><td><strong><?=$rawData["date"]?></strong></td></tr>
            <tr><td>token </td><td><strong><?=$rawData["token"]?></strong></td></tr>
            <tr><td>apikey </td><td><strong><?=$rawData["apikey"]?></strong></td></tr>
            <tr><td>provider </td><td><strong><?=($rawData["provider_name"]=="")? $rawData["providername"]:$rawData["provider_name"] ?></strong></td></tr>
            <tr><td>prov </td><td><strong><?=($rawData["prov"]=="")? $rawData["prov2"]:$rawData["prov"] ?></strong></td></tr>
            <tr><td>region </td><td><strong><?=($rawData["region"]=="")? $rawData["region2"]:$rawData["region"] ?></strong></td></tr>
            <tr><td>event </td><td><?=$Event?></td></tr>
            <tr><td>clientdetail </td><td><strong><?=$rawData["clientdetail"]?></strong></td></tr>
            <tr><td>lastupdate </td><td><strong><?=$rawData["lastupdate"]?></strong></td></tr>
        </table>
    </div>
</div>
