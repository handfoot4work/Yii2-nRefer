<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;

/**
 * Default controller for the `admin` module
 */
class LibController extends Controller
{
    public function actionHospitals($q = null)
    {
        $out = [];
        if (mb_strlen($q,'UTF-8') > 2){
            $Sql = 'SELECT * FROM lib_hospcode WHERE name LIKE "%' . $q .'%" '
                    . ' OR off_id LIKE "%' . $q .'%" '
                    . ' ORDER BY name,off_id LIMIT 20 ';
            $data = Yii::$app->db_admin->createCommand($Sql)->queryAll();
            foreach ($data as $value) {
                $out[] = ['value' => $value['off_id'].' , '.$value['name'].' , prov:'.$value['changwat'].' '.$value['typename']];
            }
        }
        echo Json::encode($out);
    }

    public function actionChospitals($q = null)
    {
        $out = [];
        if (mb_strlen($q,'UTF-8') > 2){
            $Sql = 'SELECT * FROM chospital WHERE hosname LIKE "%' . $q .'%" '
                    . ' OR hoscode LIKE "%' . $q .'%" '
                    . ' ORDER BY hosname LIMIT 20 ';
            $data = Yii::$app->db_nrefer_hdc->createCommand($Sql)->queryAll();
            foreach ($data as $value) {
                $out[] = ['value' => $value['hoscode'].' , '.$value['hosname'].' , prov:'.$value['provcode'].' Dept:'.$value['dep']];
            }
        }
        echo Json::encode($out);
    }

}
