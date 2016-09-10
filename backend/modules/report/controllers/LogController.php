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
        $Sql = 'select if(isnull(provider_name),providername,provider_name) as provider,
                        if(isnull(prov),prov2,prov) as province,
                        if(isnull(region),region2,region) as reg,
                        count(*) as no_event
                        from admin.view_event_log where substr(date,1,10)="'.$date.'"
                        group by provider,province,reg order by no_event desc';
        $rawData = Yii::$app->db_nrefer->createCommand($Sql)->queryAll();
        $dataProvider = new ArrayDataProvider([
               'allModels'=>$rawData,
               'pagination'=>['pageSize'=>50],
            ]);

        return $this->render('index',[
            'dataProvider'=>$dataProvider,
            'date'=>$date
        ]);
    }

    public function actionList($date=null)
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
            'date'=>$date
        ]);
    }

    public function actionLogDetail($id)
    {
        $Sql = 'select * from admin.view_event_log where ref='.$id;
        $rawData = Yii::$app->db_nrefer->createCommand($Sql)->queryOne();
        return $this->render('log_detail',[
            'rawData'=>$rawData,
            'id'=>$id
        ]);
    }

}
