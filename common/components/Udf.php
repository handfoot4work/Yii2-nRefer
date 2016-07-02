<?php
namespace common\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class Udf extends Component {

    public  function RealIP()
    {
        $ip = false;

        $seq = array('HTTP_CLIENT_IP',
                  'HTTP_X_FORWARDED_FOR'
                  , 'HTTP_X_FORWARDED'
                  , 'HTTP_X_CLUSTER_CLIENT_IP'
                  , 'HTTP_FORWARDED_FOR'
                  , 'HTTP_FORWARDED'
                  , 'REMOTE_ADDR');

        foreach ($seq as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return $ip;
                    }
                }
            }
        }
    }

    public function thDate($date=null)
    {
        $date = $date==""? date("Y-m-d"):$date;
        $Dates = explode('-',$date);
        if ($Dates[0]+0 >2300){
            return ($Dates[2]+0).'/'.$Dates[1].'/'.$Dates[0];
        } else {
            return ($Dates[2]+0).'/'.$Dates[1].'/'.($Dates[0]+543);
        }
    }

    public function thDateFull($date=null)
    {
        $date = $date==""? date("Y-m-d"):$date;
        $Dates = explode('-',$date);
        if ($Dates[0]+0 >2300){
            return ($Dates[2]+0).' '.$this->thMonth($Dates[1]+0).' '.$Dates[0];
        } else {
            return ($Dates[2]+0).' '.$this->thMonth($Dates[1]+0).' '.($Dates[0]+543);
        }
    }

    public function thDateAbbr($date=null)
    {
        $date = $date==""? date("Y-m-d"):$date;
        $Dates = explode('-',$date);
        if ($Dates[0]+0 >2300){
            return ($Dates[2]+0).' '.$this->thMonthAbbr($date).' '.$Dates[0];
        } else {
            return ($Dates[2]+0).' '.$this->thMonthAbbr($date).' '.($Dates[0]+543);
        }
    }

    public function thaiMonth()
    {
        return [1=>'มกราคม',2=>'กุมภาพันธ์',3=>'มีนาคม',4=>'เมษายน',5=>'พฤษภาคม',6=>'มิถุนายน',7=>'กรกฎาคม',8=>'สิงหาคม',9=>'กันยายน',10=>'ตุลาคม',11=>'พฤศจิกายน',12=>'ธันวาคม'];
    }

    public function thaiMonthAbbr()
    {
        return [1=>'มค.',2=>'กพ.',3=>'มีค.',4=>'เมย.',5=>'พค.',6=>'มิย.',7=>'กค.',8=>'สค.',9=>'กย.',10=>'ตค.',11=>'พย.',12=>'ธค.'];
    }

    public function thMonth($month=0)
    {
        $cMonth = $this->thaiMonth();
        if (strlen($month)>2){
            $Dates = explode('-',$month);
            $month = $Dates[1];
        }
        return $cMonth[$month+0];
    }

    public function thMonthAbbr($month=0)
    {
        $cMonth = $this->thaiMonthAbbr();
        if (strlen($month)>2){
            $Dates = explode('-',$month);
            $month = $Dates[1];
        }
        return $cMonth[$month+0];
    }

    public function checkTrustee($pmodule=null)
    {
        if ($pmodule=="" || !isset(Yii::$app->user->identity->employee_no) || !isset(Yii::$app->user->identity->fname) ){
            return false ;
        }
        $Sql = 'SELECT id,salaryno,title,name,surname,position,pos_level,level,office,defaultdep,defaultuni,crud,expire  '
            . ' FROM view_trustee WHERE userid="'
            .Yii::$app->user->identity->employee_no.'" AND name="'
            .Yii::$app->user->identity->fname.'" AND module=MD5("'.$pmodule.'") ';
        $userTrustee = Yii::$app->db_hospdata->createCommand($Sql)->queryOne();
        return $userTrustee ;
    }

    public function untrusteeAlert($msg=[])
    {
        echo '<h2 class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>  ท่านไม่มีสิทธิ์ใช้ระบบนี้</h2>';
    }

}
