<?php

namespace app\modules\ws\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HeaderCollection;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Request;
use yii\helpers\ArrayHelper;

class PostController extends Controller
{
    private $Agruments = [];
    private $today ;
    private $Data;
    private $User = [];

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'hospcode' => ['post'],
                ],
            ],
        ];
    }

// ========================================================================
    public function getAgrument()
// ========================================================================
    {
        $Post = Yii::$app->request->post();
        $request = Yii::$app->request;
        if (!isset($Post["data"]) || $Post["data"]=="") {

            echo json_encode([
                    'success'=>0,
                    'date_response'=>date("Y-m-d H:i:s"),
                    'error'=>"Agrument 'data' not found.",
                    'errorno'=>101,
                ]);
            exit;
            return false;
        }

        $Posts["data_format"] = (isset($Post["data_format"]) && $Post["data_format"]!="")? strtolower($Post["data_format"]):'json';
        $this->User["userid"] = isset($Post["userid"])? $Post["userid"]:'';
        $this->User["password"] = isset($Post["password"])? $Post["password"]:'';
        $Posts["security"]["userid"] = $this->User["userid"];
        $Posts["security"]["base64"] = ($Post["base64"]+0==1)? true:false;
        $Posts["security"]["trust"] = strtolower($Post["trust"])=="token"? 'token':'login';
        $Posts["security"]["token"] = isset($Post["token"])? $Post["token"]:'';

        $this->Data  = $Posts["security"]["base64"]? base64_decode($Post["data"]):$Post["data"];
        $this->Data  = $Posts["data_format"]=='json'? json_decode($this->Data):$this->Data;

        $Posts["sql_response"] = ($Post["sql_response"]+0==1)? true:false;
        $Posts["client"]["agent"] = $request->userAgent;
        $Posts["client"]["ip"] = $request->userIP;
        $Posts["client"]["host"] = $request->userHost;
        $Posts["client"]["port"] = $_SERVER["REMOTE_PORT"];
        $Posts["client"]["accept"] = $_SERVER["HTTP_ACCEPT"];
        $Posts["client"]["url"] = $request->absoluteUrl;
        $Posts["client"]["header"] = $request->headers;
        $this->Agruments = $Posts;

        return true;
    }

// ========================================================================
    public function actionIndex()
// ========================================================================
    {
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/json');
        $headers->add('Access-Control-Allow-Origin', '*');
        //$headers->add('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
        //$headers->add('Access-Control-Allow-Methods', 'GET, POST');

        $this->today = date("Y_m_d H:i:s");
        if (!($Post = $this->getAgrument())) {
            exit;
        }

        $TablesName = Yii::$app->db_nrefer_hdc->createCommand('SHOW tables')->queryAll();
        $hdcTables = $this->getHDCTable();
        $existTables = $this->getTable() ;

        foreach ($this->Data as $Table_Name=>$TableData){
            if (!array_search($Table_Name,$hdcTables) || !array_search($Table_Name,$existTables) ) {
                return json_encode([
                    'success'=>0,
                    'date_response'=>date("Y-m-d H:i:s"),
                    'error'=>'Request table name ('.$Table_Name.') not found in hdc.',
                    'errorno'=>201,
                    'request'=>$this->Agruments,
                    //'hdc'=>$HDCTable,
                    //'exist'=>$ExistTable
                ]);
            }
            $getDsc = $this->getTableDesc($Table_Name);
            $TableDescription=[];
            foreach ($getDsc as $key => $dsc) {
                $fldName = strtolower($dsc["Field"]);
                $TableDescription[$fldName]["null"] = $dsc["Null"]=='YES'? false:true ;
                $TableDescription[$fldName]["name"] = $dsc["Field"] ;
            }

            foreach ($TableData as $recno=>$row){
                $Columns = "";
                $Values = "";
                foreach ($row as $fld=>$data){
                    $fldName = strtolower($fld);
                    $Columns .= ($Columns==""? '':' , ').$TableDescription[$fldName]["name"]  ;
                    $Values .= ($Values==""? ' "':' , "').$data.'"';
                    $TableDescription[$fldName]["null"] = false ;
                }
                $lcSql[] = 'REPLACE INTO '.Yii::$app->params["table_std_prefix"].$Table_Name.' ('.$Columns.') VALUES ('.$Values.')';
                $IsFound = '';
                foreach ($TableDescription as $fldName=>$value) {
                    $IsFound .= $value["null"]? (($IsFound==''? '':', ').$TableDescription[$fldName]["name"].':null'):'' ;
                }
                $ResultSQL[] = $IsFound;
            }
        }

        $ResultSql = [];
        foreach ($lcSql as $key => $sql) {
            if ($ResultSQL[$key]==""){
                $SaveResult= Yii::$app->db_nrefer_hdc->createCommand($sql)->execute();
                $ResultSql[$key]["errorno"] = ($SaveResult==1)? 0:1;
                $ResultSql[$key]["error"] = "";
            } else {
                $ResultSql[$key]["errorno"] = 301;
                $ResultSql[$key]["error"] = $ResultSQL[$key];
            }
        }

        $Response = [
            'success'=>1,
            'date_response'=>date("Y-m-d H:i:s"),
            'request'=>$this->Agruments,
        ];

        if ($this->Agruments["sql_response"]){
            $Response["sql"] = $lcSql;
        }
        $Response["result"] = $ResultSql;
//        $Response["desc"] = $this->getTableDesc($Table_Name);

        return json_encode($Response);

        //return $this->render('index');
    }

// ========================================================================
    public function actionLib()
// ========================================================================
    {
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/json');
        $headers->add('Access-Control-Allow-Origin', '*');
        //$headers->add('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
        //$headers->add('Access-Control-Allow-Methods', 'GET, POST');

        $Post = Yii::$app->request->post();
        if (!isset($Post["type"])){
            return json_encode([
                'success'=>0,
                'date_response'=>date("Y-m-d H:i:s"),
                'error'=>'request type not found.'
            ]);
        }
        switch ($Post["type"]){
            case 'hospital':
                $Sql = "SELECT * FROM chospcode WHERE hospcode='$Post[hcode]'";
                break;
            default:
                $Sql = "";
        }
        if($Sql==""){

        } else {
            $rawData = Yii::$app->db_nrefer->createCommand($Sql)->queryAll();
            return base64_encode(json_encode([
                'success'=>1,
                'date_response'=>date("Y-m-d H:i:s"),
                'data'=>$rawData,
            ]));
        }
    }

// ========================================================================
    public function actionPut()
// ========================================================================
    {
        $Post = Yii::$app->request->post();
        echo '<pre>';
        var_dump($Post);
        echo '</pre>';
        exit;

        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/json');
        $headers->add('Access-Control-Allow-Origin:', $_SERVER['HTTP_ORIGIN']);
        $headers->add('ccess-Control-Allow-Methods', 'POST, GET, OPTIONS');
        //echo date("Y-m-d H:i:s");
        $Post = Yii::$app->request->post();
        var_dump($Post);
//        return $this->render('index');
    }

// ========================================================================
    private function getHDCTable()
// ========================================================================
    {
        $rawData = Yii::$app->db_nrefer_hdc->createCommand('SELECT * FROM standard_table')->queryAll();
        $Tables = [];
        $Tables[] = 'Unsed table name.';
        foreach ($rawData as $table) {
            $Tables[] = $table["tablename"];
        }

        return $Tables;
    }

// ========================================================================
    private function getTableDesc($TableName)
// ========================================================================
    {
        return $TableName==""? []:Yii::$app->db_nrefer_hdc->createCommand('DESCRIBE '.$TableName)->queryAll();
    }

// ========================================================================
    private function getTable()
// ========================================================================
    {
        $TablesNames = Yii::$app->db_nrefer_hdc->createCommand('SHOW tables')->queryAll();
        $Tbl = [];
        $Tbl[] = 'Unsed table name.';
        foreach ($TablesNames as $Name) {
            $Tbl[] = $Name["Tables_in_hdc_refer"];
        }
        return $Tbl;
    }

}
