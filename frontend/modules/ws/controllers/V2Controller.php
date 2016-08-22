<?php

namespace app\modules\ws\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HeaderCollection;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Request;
use yii\helpers\ArrayHelper;

class V2Controller extends Controller
{
    private $Agruments = [];
    private $today ;
    private $Data;
    private $User = [];
    private $Requests = ['secret-key-change','token-create','token-expire','status'];

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'post' => ['post'],
                    'request' => ['post'],
                    'get' => ['post'],
                    'put' => ['put'],
                ],
            ],
        ];
    }

// ========================================================================
    public function actionIndex()
// ========================================================================
    {
        return $this->render('index');
    }

// ========================================================================
    public function actionTestpost()
// ========================================================================
    {
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/json');
        $headers->add('Access-Control-Allow-Origin', '*');

        $Response = [
            'success'=>1,
            'date_response'=>date("Y-m-d H:i:s"),
            'request'=>$this->Agruments,
        ];
        echo json_encode($Response);
    }

// ========================================================================
    public function actionRequest()
// ========================================================================
    {
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/json');
        $headers->add('Access-Control-Allow-Origin', '*');

        $this->today = date("Y_m_d H:i:s");
        $Post = Yii::$app->request->post();
        if (!$this->ServerAvaliable($Post)){
            exit ;
        }

        $Req = isset($Post["request"])? $Post["request"]:'';

        if ($Req=="" || !in_array(strtolower($Req),$this->Requests) ){
            return json_encode([
                'success'=>0,
                'date_response'=>date("Y-m-d H:i:s"),
                'post'=>$Post,
                'error'=>400,
                'message_response'=>Yii::$app->params["statusCodes"][400].' (Request not found or invalid)'
            ]);
            return false;
        }

        switch ($Req){
            case 'secret-key-change':
                $Result = $this->RequestKeyChange($Post);
                break;
            case 'token-create':
                $Result = $this->RequestToken($Post);
                break;
            case 'token-expire':
                $Result = $this->RequestTokenExpire($Post);
                break;
            case 'status':
                $Result = $this->RequestStatus($Post);
                break;
        }

        $Response = [
            'success'=>$Result["success"],
            'date_response'=>date("Y-m-d H:i:s"),
        ];
        if ($Result["error"] >0){
            $Response["post"] = $Post;
        }
        if (isset($Post["keyw"])){
            $Response["keyw"] = $Post["keyw"];
        }
        if (isset($Result["token"])){
            $Response["token"] = $Result["token"];
            $Response["token_expire"] = $Result["token_expire"];
        }
        if (isset($Result["content"])){
            $Response["content"] = $Result["content"];
        }
        $Response['error'] = $Result["error"];
        $Response['message_response'] = $Result["message_response"];

        echo json_encode($Response);
    }

// ========================================================================
    private function RequestKeyChange($Post)
// ========================================================================
    {
        //        api-key => <refer provider API Key>
        //        secret-key-current => <current secret-key>
        //        secret-key-new => <new secret-key>
        $message_response = $Post["api-key"]==""? 'Not found api-key.':'';
        $message_response .= $Post["secret-key-current"]==""? 'Not found secret-key-current.':'';

        if ($Post["secret-key-new"]=="" || strlen($Post["secret-key-new"])<5){
            $message_response .= 'Not found secret-key-new or strlen less than 5.';
        }
        $InvalidChars = [" ","~","`","|",";",":","/","\\",'"',"'"] ;
        foreach ($InvalidChars as $chr) {
            if (strpos($Post["secret-key-new"],$chr)) {
                $message_response .= 'Some character in secret-key-new invalid.';
                break;
            }
        }

        if ($message_response!="") {
            return ['success'=>0,'error'=>400,'message_response'=>Yii::$app->params["statusCodes"][400].' ('.$message_response.')'];
        }

        $getUser = $this->getUser($Post["api-key"],$Post["secret-key-current"]);
        if (sizeof($getUser)==0 || $getUser["api_key"]=="") {
            return ['success'=>0,'error'=>400,'message_response'=>Yii::$app->params["statusCodes"][400].' (Incorrect api-key or secret-key-current)'];
        }

        $Sql = 'UPDATE refer_provider SET lastkeychange="'.$this->today.'" WHERE id="'.$getUser["id"].'" ';
        $updateReferProvider = Yii::$app->db_admin->createCommand($Sql)->execute();

        $Sql = 'UPDATE refer_provider SET  secret_key = "'.md5($Post["secret-key-new"]).'" '
                . ' WHERE api_key="'.$Post["api-key"].'" AND secret_key="'.md5($Post["secret-key-current"]).'" ';
        $updateProvider = Yii::$app->db_admin->createCommand($Sql)->execute();
        return ['success'=>1,'error'=>0,'message_response'=>''];
    }

// ========================================================================
    private function RequestToken($Post)
// ========================================================================
    {
        $getUser = $this->getUser($Post["api-key"],$Post["secret-key"]);
        if (sizeof($getUser)==0 || $getUser["api_key"]=="") {
            return ['success'=>0,'error'=>400,'message_response'=>Yii::$app->params["statusCodes"][400].' (Incorrect api-key or secret-key)'];
        }

        $Token = md5($this->today.$Post["secret-key"]);
        $Expire = date("Y-m-d H:i:s" ,mktime(substr($this->today,11,2)+3,substr($this->today,14,2),substr($this->today,17,2),substr($this->today,5,2),substr($this->today,8,2),substr($this->today,0,4) ) );

        $Sql = 'UPDATE refer_token SET expire="'.$this->today.'" WHERE uid="'.$getUser["id"].'" '
                . ' AND (expire="" OR ISNULL(expire) OR expire>"'.$this->today.'") ' ;
        $updateToken = Yii::$app->db_admin->createCommand($Sql)->execute();

        $Sql = 'UPDATE refer_provider SET lastlogin="'.$this->today.'" WHERE id="'.$getUser["id"].'" ';
        $updateReferProvider = Yii::$app->db_admin->createCommand($Sql)->execute();

        $Sql = 'INSERT INTO refer_token (date,uid,token,created_at,expire) '
                .' VALUES ("'.$this->today.'", "'.$getUser["id"].'" ,"'.$Token.'","'.$this->today.'" , "'.$Expire.'") ' ;
        $saveToken = Yii::$app->db_admin->createCommand($Sql)->execute();
        return ['success'=>1,'error'=>0,'token'=>$Token,'token_expire'=>$Expire,'message_response'=>$saveToken];
    }

// ========================================================================
    private function RequestTokenExpire($Post)
// ========================================================================
    {
        $Token = isset($Post["token"])? $Post["token"]:'';
        if ($Token=="") {
            return ['success'=>0,'error'=>400,'message_response'=>Yii::$app->params["statusCodes"][400].' (token not found)'];
        }

        $Sql = 'SELECT * FROM refer_token WHERE token="'.$Token.'" ' ;
        $getToken = Yii::$app->db_admin->createCommand($Sql)->queryOne();
        if (sizeof($getToken)==0 || $getToken["token"]=="" ){
            return ['success'=>0,'error'=>400,'message_response'=>Yii::$app->params["statusCodes"][400].' (invalid token)'];
        }
        $Today = mktime(substr($this->today,11,2),substr($this->today,14,2),substr($this->today,17,2),substr($this->today,5,2),substr($this->today,8,2),substr($this->today,0,4) );
        $Expire = mktime(substr($getToken["expire"],11,2),substr($getToken["expire"],14,2),substr($getToken["expire"],17,2),substr($getToken["expire"],5,2),substr($getToken["expire"],8,2),substr($getToken["expire"],0,4) );
        if ($Expire < $Today ){
            return ['success'=>0,'error'=>410,'message_response'=>Yii::$app->params["statusCodes"][410]];
        }

        $Sql = 'UPDATE refer_token SET expire="'.$this->today.'" WHERE token="'.$Token.'" ' ;
        $updateToken = Yii::$app->db_admin->createCommand($Sql)->execute();

        return ['success'=>1,'error'=>0,'token'=>$Token,'token_expire'=>$this->today,'message_response'=>$updateToken];
    }

// ========================================================================
    private function RequestStatus($Post)
// ========================================================================
    {
        $getUser = $this->getUser($Post["api-key"],$Post["secret-key"]);
        if (sizeof($getUser)==0 || $getUser["api_key"]=="") {
            return ['success'=>0,'error'=>400,'message_response'=>Yii::$app->params["statusCodes"][400].' (Incorrect api-key or secret-key-current)'];
        }

        unset($getUser["id"]);
        unset($getUser["secret_key"]);
        unset($getUser["secret_default"]);
        unset($getUser["hashing"]);
        unset($getUser["lastupdate"]);
        return ['success'=>1,'error'=>0,'message_response'=>'','content'=>$getUser];
    }

// ========================================================================
    private function getUser($apikey="",$secretkey="")
// ========================================================================
    {
        $message_response = $apikey==""? 'Not found api-key.':'';
        $message_response .= $secretkey==""? 'Not found secret-key.':'';

        if ($message_response!="") {
            echo json_encode(['success'=>0,'error'=>400,'message_response'=>Yii::$app->params["statusCodes"][400].' ('.$message_response.')']);
            exit;
        }

        $Sql = 'SELECT * FROM view_refer_provider WHERE api_key="'.$apikey.'" AND secret_key="'.md5($secretkey).'" ';
        return Yii::$app->db_admin->createCommand($Sql)->queryOne();
    }

// ========================================================================
    public function actionPost()
// ========================================================================
    {
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/json');
        $headers->add('Access-Control-Allow-Origin', '*');
        //$headers->add('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
        $headers->add('Access-Control-Allow-Methods', 'POST');

        $this->today = date("Y_m_d H:i:s");
        $Post = (array) Yii::$app->request->post();
        if (!isset($Post["data"]) || $Post["data"]=="") {
            echo json_encode([
                    'success'=>0,
                    'date_response'=>date("Y-m-d H:i:s"),
                    'error'=>400,
                    'message_response' =>Yii::$app->params["statusCodes"][400]." (Agrument 'data' not found)" ,
                ]);
            return false;
        }
        if (!$this->ServerAvaliable($Post) || !$this->checkToken($Post["token"]) ){
            exit ;
        }

        $request = Yii::$app->request;

        $Posts["data_format"] = (isset($Post["data_format"]) && $Post["data_format"]!="")? strtolower($Post["data_format"]):'json';
        $Posts["base64"] = ($Post["base64"]=='1' || $Post["base64"]=='true')? true:false;
        $Posts["sql_response"] = ($Post["sql_response"]=='1' || $Post["sql_response"]=='true')? true:false;
        $this->Data  = $Posts["base64"]? base64_decode($Post["data"]):$Post["data"];
        $this->Data  = $Posts["data_format"]=='json'? json_decode($this->Data):$this->Data;
        $Data = (array) $this->Data;

        if (sizeof($Data)==0){
            echo json_encode([
                    'success'=>0,
                    'date_response'=>date("Y-m-d H:i:s"),
                    'error'=>400,
                    'message_response' =>Yii::$app->params["statusCodes"][400]." (incorrect data)" ,
                ]);
            return false;
        }

        $this->Agruments = $Posts;

        $TablesName = Yii::$app->db_nrefer_hdc->createCommand('SHOW tables')->queryAll();
        $hdcTables = $this->getHDCTable();
        $existTables = $this->getTable() ;
        $lcSql = [];
        foreach ($this->Data as $Table_Name=>$TableData){
            if (!array_search($Table_Name,$hdcTables) || !array_search($Table_Name,$existTables) ) {
                return json_encode([
                    'success'=>0,
                    'date_response'=>date("Y-m-d H:i:s"),
                    'error'=>'Request table name ('.$Table_Name.') not found in hdc.',
                    'errorno'=>400,
                    'request'=>$this->Agruments,
                    'message_response' => Yii::$app->params["statusCodes"][400],
                ]);
            }
            $getDsc = $this->getTableDesc($Table_Name);
            $TableDescription=[];
            foreach ($getDsc as $key => $dsc) {
                $fldName = strtolower($dsc["Field"]);

                $TableDescription[$fldName]["null"] = ($dsc["Null"]=='YES' || $dsc["Extra"]=='auto_increment')? false:true ;
                $TableDescription[$fldName]["name"] = $dsc["Field"] ;
            }

            foreach ($TableData as $recno=>$row){
                $Columns = "";
                $Values = "";
                foreach ($row as $fld=>$data){
                    $fldName = strtolower($fld);
                    if ($TableDescription[$fldName]["name"] !=""){
                        $Columns .= ($Columns==""? '':' , ').$TableDescription[$fldName]["name"]  ;
                        $Values .= ($Values==""? ' "':' , "').$data.'"';
                    }
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
        //echo json_encode($lcSql);
        //exit;
        $ResultSql = [];
        foreach ($lcSql as $key => $sql) {
            if ($ResultSQL[$key]==""){
                $SaveResult= Yii::$app->db_nrefer_hdc->createCommand($sql)->execute();
                if ($this->Agruments["sql_response"]){
                    $ResultSql[$key]["sql"] = $lcSql[$key];
                }
                $ResultSql[$key]["errorno"] = ($SaveResult==1)? 0:1;
                $ResultSql[$key]["error"] = "";
            } else {
                if ($this->Agruments["sql_response"]){
                    $ResultSql[$key]["sql"] = $lcSql[$key];
                }
                $ResultSql[$key]["errorno"] = 301;
                $ResultSql[$key]["error"] = $ResultSQL[$key];
            }
        }

        $Response = [
            'success'=>1,
            'date_response'=>date("Y-m-d H:i:s"),
            'request'=>$this->Agruments,
        ];

        $Response["result"] = $ResultSql;

        return json_encode($Response);
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
    private function read_data($param)
// ========================================================================
{
    //if (!$this->ServerAvaliable($param) || !$this->checkToken($param["token"]) ){
    //    exit ;
    //}

    if (!$this->checkToken($param["token"]) ){
        exit ;
    }
    $return = [
                'success'=>1,
                'date_response'=>$this->today,
                'keyw'=>$keyw,
                'error'=>0,
                'message_response' =>'' ,
                'params'=>$param
            ];
    return json_encode($return);
}

// ========================================================================
    public function actionData($token=null,$type=send,$seq=null,$pid=null,
                                $an=null,$cid=null,$prov=null,$lastupdate=null,
                                $date=null,$keyw=null)
// ========================================================================
    {
        $this->today = date("Y_m_d H:i:s");
        if ($token=="" || $cid=="" || ($seq=="" && $pid=="" && $an=="" && $prov=="" && $date=="" && $lastupdate=="" )) {
            echo json_encode([
                    'success'=>0,
                    'date_response'=>$this->today,
                    'keyw'=>$keyw,
                    'error'=>400,
                    'message_response' =>Yii::$app->params["statusCodes"][400]." (Not found any agrument for search data.)" ,
                ]);
            return false;
        }
        return $this->read_data([
            'token'=>$token,
            'type'=>$type,
            'seq'=>$seq,
            'pid'=>$pid,
            'an'=>$an,
            'cid'=>$cid,
            'prov'=>$prov,
            'date'=>$date,
            'keyw'=>$keyw,
        ]);
    }

// ========================================================================
    public function actionGet()
// ========================================================================
    {
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/json');
        $headers->add('Access-Control-Allow-Origin', '*');
        //$headers->add('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
        //$headers->add('Access-Control-Allow-Origin:', $_SERVER['HTTP_ORIGIN']);
        //$headers->add('ccess-Control-Allow-Methods', 'POST, GET, OPTIONS');
        $headers->add('Access-Control-Allow-Methods', 'POST');

        $this->today = date("Y_m_d H:i:s");
        $Post = (array) Yii::$app->request->post();
        if ($Post["token"]=="" || ($Post["date"]=="" && $Post["cid"]=="") ) {
            echo json_encode([
                    'success'=>0,
                    'date_response'=>date("Y-m-d H:i:s"),
                    'error'=>400,
                    'message_response' =>Yii::$app->params["statusCodes"][400]." (Not found any agrument for search data.)" ,
                ]);
            return false;
        }
        if (!$this->checkServerAvaliable() || !$this->checkToken($Post["token"]) ){
            exit ;
        }

        if ($Post["date"] != ""){
            return json_encode($this->GetByDate($Post));
        } else if ($seq != ""){
            return json_encode($this->GetBySeq($Post));
        }

        return json_encode([
            'success'=>0,
            'date_response'=>date("Y-m-d H:i:s"),
            'post'=>$Post,
            'error'=>404,
            'message_response'=>Yii::$app->params["statusCodes"][404],
        ]);
    }

// ========================================================================
    public function actionGetHistory($token=null,$type=send,$seq=null,$pid=null,$an=null,$cid=null,$prov=null,$lastupdate=null,$date=null)
// ========================================================================
    {
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/json');
        $headers->add('Access-Control-Allow-Origin', '*');

        echo date("Y-m-d H:i:s");

        $Sql = "select * from person limit 1";
        $referHistory= Yii::$app->db_nrefer_hdc->createCommand($Sql)->queryAll();
        print_r($rawData);

        $rawData = Yii::$app->db_hadoop->createCommand($Sql)->queryAll();
        print_r($rawData);

        exit;

        $this->today = date("Y_m_d H:i:s");
        $Post = (array) Yii::$app->request->post();
        if ($seq=="" && $pid=="" && $an=="" && $cid=="" && $prov=="" && $date=="" && $lastupdate=="" ) {
            echo json_encode([
                    'success'=>0,
                    'date_response'=>date("Y-m-d H:i:s"),
                    'error'=>400,
                    'message_response' =>Yii::$app->params["statusCodes"][400]." (Not found any agrument for search data.)" ,
                ]);
            return false;
        }
        if (!$this->ServerAvaliable($Post) || !$this->checkToken($token) ){
            exit ;
        }

        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/json');
        $headers->add('Access-Control-Allow-Origin:', $_SERVER['HTTP_ORIGIN']);
        $headers->add('ccess-Control-Allow-Methods', 'POST, GET, OPTIONS');
        if ($date != ""){
            return json_encode($this->GetByDate($token,$type,$date));
        } else if ($seq != ""){
            return json_encode($this->GetBySeq($token,$type,$seq));
        }

        return json_encode([
            'success'=>0,
            'date_response'=>date("Y-m-d H:i:s"),
            'post'=>$Post,
            'error'=>404,
            'token'=>$token,
            'seq'=>$seq,
            'pid'=>$pid,
            'an'=>$an,
            'cid'=>$cid,
            'prov'=>$prov,
            'lastupdate'=>$lastupdate,
            'date'=>$date,
            'message_response'=>Yii::$app->params["statusCodes"][404],
        ]);
    }

// ========================================================================
    private function GetBySeq($Post)
// ========================================================================
    {
        $Sql = 'SELECT * FROM view_refer_token WHERE token="'.$Post["token"].'" ';
        $getToken = Yii::$app->db_admin->createCommand($Sql)->queryOne();

        $Sql = 'SELECT r.* FROM refer_history r LEFT JOIN chospital h on r.HOSPCODE=h.hoscode '
                . ' WHERE SEQ="'.$Post["seq"].'" AND h.provcode in ('.$getToken["prov"].') ';
        $referHistory= Yii::$app->db_nrefer_hdc->createCommand($Sql)->queryAll();
        $pid = $referHistory[0]["PID"];
        $hospcode = $referHistory[0]["HOSPCODE"];

        return [
            'success'=>1,
            'date_response'=>$this->today,
            'message_response'=>'',
            'data'=>[
                'refer_history',$referHistory
            ],
        ];
    }

    // ========================================================================
        private function GetByDate($Post)
    // ========================================================================
        {
            $Sql = 'SELECT * FROM view_refer_token WHERE token="'.$Post["token"].'" ';
            $getToken = Yii::$app->db_admin->createCommand($Sql)->queryOne();

            if (strtolower($Post["type"])=='send'){
                $Sql = (isset($Post["individual"]) && $Post["individual"]+0==1? 'SELECT r.* ':'SELECT count(1) as cases ')
                        . ' FROM refer_history r LEFT JOIN chospital h on r.HOSPCODE=h.hoscode '
                        . ' WHERE substr(r.DATETIME_REFER,1,10)="'.$Post["date"].'" '
                        . ' AND h.provcode in ('.$getToken["prov"].') ';
            } else {
                $Sql = (isset($Post["individual"]) && $Post["individual"]+0==1? 'SELECT r.* ':'SELECT count(1) as cases ')
                        . ' FROM refer_history r LEFT JOIN chospital d on r.HOSP_DESTINATION=d.hoscode '
                        . ' LEFT JOIN chospital h on r.HOSPCODE=h.hoscode '
                        . ' WHERE substr(r.DATETIME_REFER,1,10)="'.$Post["date"].'" AND r.HOSP_DESTINATION!="" '
                        . ' AND d.provcode in ('.$getToken["provgroup"].')  ';
                        //. ' AND d.provcode in ('.$getToken["provgroup"].') AND  h.provcode!="'.$getToken["prov"].'" ';
            }
            if (isset($Post["cid"]) && $Post["cid"]!=""){
                //$Sql .= ' AND r.CID ="'.str_replace([" ",";"],["",""],$Post["cid"]).'"';
            }
            if (isset($Post["limit"]) && $Post["limit"]+0>0 && isset($Post["start"]) ){
                $Sql .= ' LIMIT '.($Post["start"]+0).' , '.($Post["limit"]+0);
            } else {
                $Sql .= ' LIMIT  0,10 ';
            }

            $referHistory= Yii::$app->db_nrefer_hdc->createCommand($Sql)->queryAll();
            $pid = $referHistory[0]["PID"];
            $hospcode = $referHistory[0]["HOSPCODE"];

            return [
                'success'=>1,
                'date_response'=>$this->today,
                'message_response'=>'',
                'data'=>[
                    'refer_history',$referHistory
                ],
            ];
        }

// ========================================================================
    public function actionPut()
// ========================================================================
    {
        $Post = Yii::$app->request->post();

        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/json');
        $headers->add('Access-Control-Allow-Origin:', $_SERVER['HTTP_ORIGIN']);
        $headers->add('ccess-Control-Allow-Methods', 'POST, GET, OPTIONS');

        return json_encode([
            'success'=>0,
            'date_response'=>date("Y-m-d H:i:s"),
            'post'=>$Post,
            'error'=>404,
            'message_response'=>Yii::$app->params["statusCodes"][404],
        ]);
    }

// ========================================================================
    private function checkToken($token="")
// ========================================================================
    {
        if ($token=="") {
            echo json_encode(['success'=>0,'date_response'=>$this->today,'error'=>400,'message_response'=>Yii::$app->params["statusCodes"][400].' (token not found)']);
            return false;
        }

        $Sql = 'SELECT * FROM view_refer_token WHERE token="'.$token.'" ';
        $getToken = Yii::$app->db_admin->createCommand($Sql)->queryOne();

        if ($getToken["token"]=="") {
            echo json_encode(['success'=>0,'date_response'=>$this->today,'error'=>400,'message_response'=>Yii::$app->params["statusCodes"][400].' (token not valid)']);
            return false;
        }
        $Today = mktime(substr($this->today,11,2),substr($this->today,14,2),substr($this->today,17,2),substr($this->today,5,2),substr($this->today,8,2),substr($this->today,0,4) );
        $Expire = mktime(substr($getToken["expire"],11,2),substr($getToken["expire"],14,2),substr($getToken["expire"],17,2),substr($getToken["expire"],5,2),substr($getToken["expire"],8,2),substr($getToken["expire"],0,4) );
        if ($Expire < $Today ){
            echo json_encode(['success'=>0,'date_response'=>$this->today,'error'=>410,'message_response'=>Yii::$app->params["statusCodes"][410]]);
            return false;
        }

        return true;
    }

// ========================================================================
    private function checkServerAvaliable()
// ========================================================================
    {
        if (Yii::$app->params["serverMaintenance"]){
            echo json_encode([
                'success'=>0,
                'date_response'=>$this->today,
                'error'=>501,
                'message_response'=>Yii::$app->params["statusCodes"][501],
            ]);
            return false;
        }
        return true;
    }

// ========================================================================
    private function ServerAvaliable($Post)
// ========================================================================
    {
        if (Yii::$app->params["serverMaintenance"]){
            echo json_encode([
                'success'=>0,
                'date_response'=>$this->today,
                'error'=>501,
                'message_response'=>Yii::$app->params["statusCodes"][501],
            ]);
            return false;
        }
        if (isset($Post["api-key"]) && $Post["api-key"]!="" && isset($Post["secret-key"]) && $Post["secret-key"]!="" ){
            $Sql = 'SELECT * FROM refer_provider '
                        .' WHERE api_key="'.$Post["api-key"].'" '
                        .' AND secret_key="'.md5($Post["secret-key"]).'" '
                        .' AND secret_default="'.$Post["secret-key"].'" ';
            $getProvider = Yii::$app->db_admin->createCommand($Sql)->queryOne();
            if (sizeof($getProvider) >0 && $getProvider["api_key"]==$Post["api-key"]){
                echo json_encode([
                    'success'=>0,
                    'date_response'=>$this->today,
                    'error'=>401,
                    'message_response'=>Yii::$app->params["statusCodes"][401].' (must be change initial secret-key)',
                ]);
                return false;
            }
        }
        return true;
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
