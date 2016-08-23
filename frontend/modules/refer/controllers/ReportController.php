<?php
namespace app\modules\refer\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

/**
 * Default controller for the `ws` module
 */
class ReportController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionSum_hcode($tablename='refer_history',$hospcode=null,$date1=null,$date2=null,$changwat=null,$region=null)
    {
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/json');
        $headers->add('Access-Control-Allow-Origin', '*');
        //$headers->add('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
        //$headers->add('Access-Control-Allow-Methods', 'GET, POST');

        if (isset($_POST["date1"]) && $_POST["date1"]!=""){
            $date1 = $_POST["date1"];
            $date2 = $_POST["date2"];
        } else {
            $date1 = isset($_GET["date1"])? $_GET["date1"]:date("Y-m-d");
            $date2 = isset($_GET["date2"])? $_GET["date2"]:date("Y-m-d");
        }
        $date1 = $date1==""? date("Y-m-d"):$date1;
        $date2 = $date2==""? date("Y-m-d"):$date2;

        $Where = 'substr(r.D_UPDATE,1,10) BETWEEN "'.$date1.'" AND "'.$date2.'" AND length(r.HOSPCODE)=5 ';
        $Where .= $changwat==""? '':(' AND hosp.provcode="'.$changwat.'" ');
        $Sql = 'SELECT  "'.$date1.'" as date, c.zonecode as region,hosp.provcode,c.changwatname, '
                . ' r.HOSPCODE as hcode,hosp.hosname as hname,count(*) as cases '
                . ', sum(if(r.AN="" OR ISNULL(r.AN) , 1 , 0 )) as opd '
                . ', sum(if(r.AN="" OR ISNULL(r.AN) , 0 , 1 )) as ipd '
                . ', sum(if(hosp.provcode=d.provcode , 1 , 0 )) as inprov '
                . ', sum(if(c.zonecode=c2.zonecode , 1 , 0 )) as inregion '
                . 'FROM '.$tablename.' r LEFT JOIN chospital_copy hosp ON r.HOSPCODE=hosp.hoscode '
                . '     LEFT JOIN cchangwat c ON hosp.provcode=c.changwatcode '
                . '     LEFT JOIN chospital_copy d ON r.HOSP_DESTINATION=d.hoscode '
                . '     LEFT JOIN cchangwat c2 ON d.provcode=c2.changwatcode '
                . 'WHERE '.$Where.' GROUP BY r.HOSPCODE '
                .'ORDER BY c.zonecode,hosp.provcode,cases DESC';
        $rawData = Yii::$app->db_nrefer->createCommand($Sql)->queryAll();
        if (isset($_GET["ws"]) && $_GET["ws"]==true){
            return json_encode([
                    'success'=>true,
                    'date_response'=>date("Y-m-d H:i:s"),
                    'error'=>0,
                    'data'=>$rawData,
                    'client_agent'=>$_SERVER['HTTP_USER_AGENT'],
                    'client_ip'=>$_SERVER['REMOTE_ADDR'],
                ]);
        } else {
            $dataProvider = new ArrayDataProvider([
                    'allModels'=>$rawData,
                    'pagination'=>['pageSize'=>100],
                ]);
            return $this->render('sum_hcode',[
                'dataProvider'=>$dataProvider,
                'tablename'=>$tablename,
                'rawData'=>$rawData,
                'date1'=>$date1,
                'date2'=>$date2,
                'changwat'=>$changwat,
                'region'=>$region,
            ]);
        }
    }

    public function actionSum_prov($tablename='refer_history',$hospcode=null,$date1=null,$date2=null,$region=null)
    {
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/json');
        $headers->add('Access-Control-Allow-Origin', '*');

        if (isset($_POST["date1"]) && $_POST["date1"]!=""){
            $date1 = $_POST["date1"];
            $date2 = $_POST["date2"];
        } else {
            $date1 = isset($_GET["date1"])? $_GET["date1"]:date("Y-m-d");
            $date2 = isset($_GET["date2"])? $_GET["date2"]:date("Y-m-d");

            $date1 = str_replace([" ",";"," or "],["","",""],$date1);
            $date2 = str_replace([" ",";"],["",""],$date2);

        }
        $date1 = $date1==""? date("Y-m-d"):$date1;
        $date2 = $date2==""? date("Y-m-d"):$date2;

        $Where = ' substr(r.D_UPDATE,1,10) BETWEEN "'.$date1.'" AND "'.$date2.'" AND length(r.HOSPCODE)=5 ';
        $Where .= $region==""? '':(' AND c.zonecode="'.$region.'" ');
        $Sql = 'SELECT  "'.$date1.'" as date1,"'.$date2.'" as date2, c.zonecode as region,hosp.provcode,c.changwatname ,count(*) as cases '
                . ', sum(if(r.AN="" OR ISNULL(r.AN) , 1 , 0 )) as opd '
                . ', sum(if(r.AN="" OR ISNULL(r.AN) , 0 , 1 )) as ipd '
                . ', sum(if(hosp.provcode=d.provcode , 1 , 0 )) as inprov '
                . ', sum(if(c.zonecode=c2.zonecode , 1 , 0 )) as inregion '
                . ' FROM '.$tablename.' r LEFT JOIN chospital_copy hosp ON r.HOSPCODE=hosp.hoscode '
                . '     LEFT JOIN cchangwat c ON hosp.provcode=c.changwatcode '
                . '     LEFT JOIN chospital_copy d ON r.HOSP_DESTINATION=d.hoscode '
                . '     LEFT JOIN cchangwat c2 ON d.provcode=c2.changwatcode '
                . ' WHERE  '.$Where
                . ' GROUP BY hosp.provcode '
                . ' ORDER BY c.zonecode,cases DESC';
        $rawData = Yii::$app->db_nrefer->createCommand($Sql)->queryAll();
        if (isset($_GET["ws"]) && $_GET["ws"]==true){
            return json_encode([
                    'success'=>true,
                    'date_response'=>date("Y-m-d H:i:s"),
                    'error'=>0,
                    'data'=>$rawData,
                    'client_agent'=>$_SERVER['HTTP_USER_AGENT'],
                    'client_ip'=>$_SERVER['REMOTE_ADDR'],
                ]);
        } else {
            $dataProvider = new ArrayDataProvider([
                    'allModels'=>$rawData,
                    'pagination'=>['pageSize'=>100],
                ]);
            return $this->render('sum_prov',[
                'dataProvider'=>$dataProvider,
                'tablename'=>$tablename,
                'rawData'=>$rawData,
                'date1'=>$date1,
                'date2'=>$date2,
                'region'=>$region,
            ]);
        }
    }

    public function actionSum_region($tablename='refer_history',$hospcode=null,$date1=null,$date2=null,$region=null)
    {
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/json');
        $headers->add('Access-Control-Allow-Origin', '*');

        $Sql = 'SELECT * FROM cregion WHERE name!="" and isactive=1 ORDER BY name ';
        $rg = Yii::$app->db_nrefer->createCommand($Sql)->queryAll();
        $regions = yii\helpers\ArrayHelper::map($rg,"code","name");

        if (isset($_POST["date1"]) && $_POST["date1"]!=""){
            $date1 = $_POST["date1"];
            $date2 = $_POST["date2"];
        } else {
            $date1 = isset($_GET["date1"])? $_GET["date1"]:date("Y-m-d");
            $date2 = isset($_GET["date2"])? $_GET["date2"]:date("Y-m-d");
        }
        $date1 = $date1==""? date("Y-m-d"):$date1;
        $date2 = $date2==""? date("Y-m-d"):$date2;

        $Where = ' substr(r.D_UPDATE,1,10) BETWEEN "'.$date1.'" AND "'.$date2.'" AND length(r.HOSPCODE)=5 ';
        $Sql = 'SELECT "'.$date1.'" as date1, "'.$date2.'" as date2, c.zonecode as region ,count(*) as cases '
                . ', sum(if(r.AN="" OR ISNULL(r.AN) , 1 , 0 )) as opd '
                . ', sum(if(r.AN="" OR ISNULL(r.AN) , 0 , 1 )) as ipd '
                . ', sum(if(hosp.provcode=d.provcode , 1 , 0 )) as inprov '
                . ', sum(if(c.zonecode=c2.zonecode , 1 , 0 )) as inregion '
                . ' FROM '.$tablename.' r LEFT JOIN chospital_copy hosp ON r.HOSPCODE=hosp.hoscode '
                . '     LEFT JOIN cchangwat c ON hosp.provcode=c.changwatcode '
                . '     LEFT JOIN chospital_copy d ON r.HOSP_DESTINATION=d.hoscode '
                . '     LEFT JOIN cchangwat c2 ON d.provcode=c2.changwatcode '
                . ' WHERE  '.$Where
                . ' GROUP BY c.zonecode ';
        $rawData = Yii::$app->db_nrefer->createCommand($Sql)->queryAll();
        $Data = [];
        foreach ($rawData as $value) {
            $Data[$value["region"]] = $value;
        }

        $rawData = [];
        $Cases = [];
        $Cases["cases"] = 0;
        $Cases["outregion"] = 0;
        foreach ($regions as $rc=>$region) {
            if (isset($Data[$region])) {
                $rawData[$region] = $Data[$region];
            } else {
                $rawData[$region] = ["date1"=> $date1,"date2"=> $date2, "region" => $region,"cases" => 0 ,
                    "opd" => 0, "ipd" => 0, "inprov" => 0 ,"inregion" => 0];
            }
            $Cases["cases"] += $region["cases"];
            $Cases["outregion"] += $region["cases"]-$region["inregion"];
        }

        $Cases["percent"] = $Cases["cases"]==0? 0:($Cases["outregion"]*100/$Cases["cases"]);

        if (isset($_GET["ws"]) && $_GET["ws"]==true){
            return json_encode([
                    'success'=>true,
                    'date_response'=>date("Y-m-d H:i:s"),
                    'error'=>0,
                    'data'=>$rawData,
                    'client_agent'=>$_SERVER['HTTP_USER_AGENT'],
                    'client_ip'=>$_SERVER['REMOTE_ADDR'],
                ]);
        } else {
            $dataProvider = new ArrayDataProvider([
                    'allModels'=>$rawData,
                    'pagination'=>['pageSize'=>100],
                ]);

            return $this->render('sum_region',[
                'dataProvider'=>$dataProvider,
                'tablename'=>$tablename,
                'rawData'=>$rawData,
                'date1'=>$date1,
                'date2'=>$date2,
                'regions'=>$regions,
                'Cases'=>$Cases
            ]);
        }

    }

    public function actionTable($tablename='refer_history',$hospcode='00000',$date1=null,$date2=null,$region=null,$changwat=null)
    {
        if (isset($_POST["date1"]) && $_POST["date1"]!=""){
            $date1 = $_POST["date1"];
            $date2 = $_POST["date2"];
        } else {
            $date1 = isset($_GET["date1"])? $_GET["date1"]:date("Y-m-d");
            $date2 = isset($_GET["date2"])? $_GET["date2"]:date("Y-m-d");
        }
        $date1 = $date1==""? date("Y-m-d"):$date1;
        $date2 = $date2==""? date("Y-m-d"):$date2;

        $Where = isset($hospcode)? (' AND r.HOSPCODE="'.$hospcode.'"  '):'';
        $Sql = 'SELECT "'.$date1.'" as date, "'.$date2.'" as date2,  hosp.hospname, h.hosname as hname_destination, '
                    . ' h.provcode as changwat_destination, c.changwatname, r.* '
                    . ' FROM '.$tablename.' r LEFT JOIN chospcode hosp ON r.HOSPCODE=hosp.hospcode '
                    . ' LEFT JOIN chospital_copy h ON r.HOSP_DESTINATION=h.hoscode'
                    . ' LEFT JOIN cchangwat c ON h.provcode=c.changwatcode'
                    . ' WHERE substr(r.D_UPDATE,1,10) BETWEEN "'.$date1.'" AND "'.$date2.'" '.$Where.'  AND length(r.HOSPCODE)=5 '
                    . ' ORDER BY r.HOSPCODE,r.D_UPDATE DESC LIMIT 2500';
        $rawData = Yii::$app->db_nrefer->createCommand($Sql)->queryAll();
        if (isset($_GET["ws"]) && $_GET["ws"]==true){
            return json_encode([
                    'success'=>true,
                    'date_response'=>date("Y-m-d H:i:s"),
                    'error'=>0,
                    'hospcode'=>$hospcode,
                    'hospname'=>$rawData[0]["hospname"],
                    'data'=>["$tablename"=>$rawData],
                    'client_agent'=>$_SERVER['HTTP_USER_AGENT'],
                    'client_ip'=>$_SERVER['REMOTE_ADDR'],
                ]);
        } else {
            $dataProvider = new ArrayDataProvider([
                    'allModels'=>$rawData,
                    'pagination'=>['pageSize'=>100],
                ]);
            return $this->render('showtable',[
                'dataProvider'=>$dataProvider,
                'tablename'=>$tablename,
                'rawData'=>$rawData,
                'hospcode'=>$hospcode,
                'date1'=>$date1,
                'date2'=>$date2,
                'region'=>$region,
                'changwat'=>$changwat
            ]);
        }
    }
}
