<?php

namespace app\modules\ws\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HeaderCollection;
//use yii\web\Response ;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Request;
use yii\helpers\ArrayHelper;

/**
 * Default controller for the `ws` module
 */
class GetController extends Controller
{
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

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLib()
    {
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/json');

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

    public function actionPut()
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

    public function actionHospcode($hcode=null)
    {
        echo 'test '.$_SERVER['HTTP_ORIGIN'];
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/json');
        $headers->add('Access-Control-Allow-Origin:', $_SERVER['HTTP_ORIGIN']);
        $headers->add('ccess-Control-Allow-Methods', 'POST, GET, OPTIONS');
        $headers->add('Access-Control-Max-Age', 1000);
        $headers->add('Access-Control-Allow-Headers', 'Content-Type');
//        header('Content-type: application/json');
//        header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
//        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
//        header('Access-Control-Max-Age: 1000');
//        header('Access-Control-Allow-Headers: Content-Type');

        return $this->render('hospcode');
    }
}
