<?php

namespace backend\modules\report\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

class LogController extends \yii\web\Controller
{
    public function actionIndex($date=null)
    {
        $date = $date==""? date("Y-m-d"):$date;
        $Sql = 'select * from admin.view_event_log where substr(date,1,10)="'.$date.'" order by ref desc limit 500';
        $rawData = Yii::$app->db_nrefer->createCommand($Sql)->queryAll();
        $dataProvider = new ArrayDataProvider([
               'allModels'=>$rawData,
               'pagination'=>['pageSize'=>50],
            ]);

        return $this->render('log_list',[
            'dataProvider'=>$dataProvider,
        ]);
    }

    public function actionLogDetail($id)
    {
        $Sql = 'select * from admin.view_event_log where ref='.$id;
        $rawData = Yii::$app->db_nrefer->createCommand($Sql)->queryOne();
        return $this->render('log_detail',[
            'rawData'=>$rawData,
        ]);
    }

}
