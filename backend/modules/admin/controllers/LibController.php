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
        if (mb_strlen($q,'UTF-8') > 1){
            $Sql = 'SELECT * FROM admin.lib_hospcode WHERE name LIKE "%' . $q .'%" '
                    . ' OR off_id LIKE "%' . $q .'%" '
                    . ' ORDER BY name,off_id LIMIT 20 ';
            $data = Yii::$app->db_admin->createCommand($Sql)->queryAll();
            foreach ($data as $value) {
                $out[] = ['value' => $value['off_id'].' , '.$value['name'].' , prov:'.$value['changwat'].' '.$value['typename']];
            }
        }
        echo Json::encode($out);
    }

}
