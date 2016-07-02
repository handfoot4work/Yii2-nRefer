<?php
namespace app\modules\refer\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;

/**
 * Default controller for the `ws` module
 */
class MonitorController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionTable()
    {
        return $this->redirect(Url::to(['/refer/report/table']));
    }

    public function actionTable1($tablename='refer_history',$hospcode='00000',$date1=null,$date2=null,$region=null,$changwat=null)
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
        $Sql = 'SELECT"'.$date.'" as date,  hosp.hospname, h.hosname as hname_destination, '
                    . ' h.provcode as changwat_destination, c.changwatname, r.* '
                    . ' FROM '.$tablename.' r LEFT JOIN chospcode hosp ON r.HOSPCODE=hosp.hospcode '
                    . ' LEFT JOIN chospital_copy h ON r.HOSP_DESTINATION=h.hoscode'
                    . ' LEFT JOIN cchangwat c ON h.provcode=c.changwatcode'
                    . ' WHERE substr(r.D_UPDATE,1,10) BETWEEN "'.$date2.'" AND "'.$date2.'" '.$Where.'  AND length(r.HOSPCODE)=5 '
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
