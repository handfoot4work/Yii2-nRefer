<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\admin\ReferProvider;
use app\models\admin\ReferProviderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\filters\AccessRule;

/**
 * ReferproviderController implements the CRUD actions for ReferProvider model.
 */
class ReferproviderController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
			        'class' => AccessRule::className(),
			    ],
                'rules' => [
                    [
                        'allow' => in_array(Yii::$app->user->identity->username , Yii::$app->params["admin"]['referProvider']),
                        'roles' => ['@'], 
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all ReferProvider models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReferProviderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        /*$Sql = 'SELECT * FROM mophrefer.cchangwat ORDER BY changwatcode ';
        $rawData = Yii::$app->db_admin->createCommand($Sql)->queryAll();
        $SpChar = '!@#$%*-<>';
        foreach ($rawData as $key => $value) {
            $SecretKey = '';
            $Hashing = '';
            for ($i=1; $i<9; $i++){
                $Rnd = rand(1,7);
                switch ($Rnd){
                    case 1:
                    case 4:
                        $SecretKey .= chr(rand(65,90));
                        $Hashing .= chr(rand(65,90));
                        break;
                    case 2:
                    case 5:
                        $SecretKey .= chr(rand(97,122));
                        $Hashing .= chr(rand(97,122));
                        break;
                    case 3:
                    case 6:
                        $SecretKey .= rand(0,9);
                        $Hashing .= rand(0,9);
                        break;
                    default:
                        $SecretKey .= substr($SpChar,rand(0,9),1);
                        $Hashing .= substr($SpChar,rand(0,9),1);
                }
            }
            $ApiKey = MD5('THAI REFER'.$Hashing);
            $Sql = 'INSERT INTO refer_provider (date_register,prov,region,provider, responder,'
                . ' usage_group,secret_key,secret_default,hashing,api_key) '
                . ' VALUES ("2016-01-01", "'.$value["changwatcode"].'" , "'.$value["zonecode"].'" , "1" , "'
                . $value["changwatname"].'", "P" , "'.$SecretKey.'" , "'
                . $SecretKey.'" , "'.$Hashing.'" , "'.$ApiKey.'") ';
            echo '<br>',$Sql;
            echo Yii::$app->db_admin->createCommand($Sql)->execute();
        }
        exit;
        */

        $Sql = 'SELECT ref, name, concat(name," ",owner) as sname FROM refer_name WHERE date_expire="0000-00-00" OR isnull(date_expire) ';
        $rawData = Yii::$app->db_admin->createCommand($Sql)->queryAll();
        $referName = yii\helpers\ArrayHelper::map($rawData,'ref',"sname");

//        SELECT name,changwat,ampur,code5,gps_latitude,gps_longitude FROM `lib_hospcode` WHERE typecode in ("05","06") and gps_latitude>0 and gps_longitude>0
//        GROUP BY changwat
//        order by changwat;

        $Sql = 'SELECT p.*, n.ref as referid, n.name, n.owner,h.gps_latitude,h.gps_longitude, c.changwatname, n.marker '
                . ' FROM refer_provider p LEFT JOIN refer_name n'
                . ' ON p.provider=n.ref LEFT JOIN lib_hospcode h '
                . ' ON p.prov=h.changwat and h.typecode in ("05","06") and h.gps_latitude>0 and h.gps_longitude>0 '
                . ' LEFT JOIN cchangwat c ON p.prov=c.changwatcode '
                . ' GROUP BY p.id '
                . ' ORDER BY p.region,p.prov ';
        $rawData = Yii::$app->db_admin->createCommand($Sql)->queryAll();
        $referProvider = [];
        foreach ($rawData as $key => $value) {
            $referProvider[$value["prov"]]["changwatname"] = $value["changwatname"];
            $referProvider[$value["prov"]]["region"] = $value["region"];
            $referProvider[$value["prov"]]["name"] = $value["name"];
            $referProvider[$value["prov"]]["owner"] = $value["owner"];
            $referProvider[$value["prov"]]["responder"] = $value["responder"];
            $referProvider[$value["prov"]]["type"] = $value["usage_group"]=='R'? 'เขต':'จังหวัด';
            $referProvider[$value["prov"]]['lat'] = $value["gps_latitude"] ;
            $referProvider[$value["prov"]]['long'] =$value["gps_longitude"];
            $referProvider[$value["prov"]]['marker'] =$value["marker"];
            $referProvider[$value["prov"]]['referid'] =$value["referid"];
        }

        $Sql = 'SELECT changwatcode, concat(changwatname," - ",changwatcode) as changwatname '
                .' FROM mophrefer.cchangwat ORDER BY changwatname ';
        $rawData = Yii::$app->db_admin->createCommand($Sql)->queryAll();
        $changwatName = yii\helpers\ArrayHelper::map($rawData,"changwatcode","changwatname");

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'referName'=>$referName,
            'changwatName'=>$changwatName,
            'referProvider'=>$referProvider,
        ]);
    }

    /**
     * Displays a single ReferProvider model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $Sql = 'SELECT ref, name, concat(name," ",owner) as sname FROM refer_name WHERE date_expire="0000-00-00" OR isnull(date_expire) ';
        $rawData = Yii::$app->db_admin->createCommand($Sql)->queryAll();
        $referName = yii\helpers\ArrayHelper::map($rawData,'ref',"sname");

        $Sql = 'SELECT changwatcode, concat(changwatname," - ",changwatcode) as changwatname '
                .' FROM mophrefer.cchangwat ORDER BY changwatname ';
        $rawData = Yii::$app->db_admin->createCommand($Sql)->queryAll();
        $changwatName = yii\helpers\ArrayHelper::map($rawData,"changwatcode","changwatname");

        return $this->render('view', [
            'model' => $this->findModel($id),
            'referName'=>$referName,
            'changwatName'=>$changwatName,
        ]);
    }

    /**
     * Creates a new ReferProvider model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $Sql = 'SELECT ref, name, concat(name," ",owner) as sname FROM refer_name WHERE date_expire="0000-00-00" OR isnull(date_expire) ';
        $rawData = Yii::$app->db_admin->createCommand($Sql)->queryAll();
        $referName = yii\helpers\ArrayHelper::map($rawData,"ref","sname");

        $Sql = 'SELECT changwatcode, concat(changwatname," - ",changwatcode) as changwatname '
                .' FROM mophrefer.cchangwat ORDER BY changwatname ';
        $rawData = Yii::$app->db_admin->createCommand($Sql)->queryAll();
        $changwatName = yii\helpers\ArrayHelper::map($rawData,"changwatcode","changwatname");

        $model = new ReferProvider();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'referName'=>$referName,
                'changwatName'=>$changwatName,
            ]);
        }
    }

    /**
     * Updates an existing ReferProvider model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $Sql = 'SELECT ref, name, concat(name," ",owner) as sname FROM refer_name WHERE date_expire="0000-00-00" OR isnull(date_expire) ';
        $rawData = Yii::$app->db_admin->createCommand($Sql)->queryAll();
        $referName = yii\helpers\ArrayHelper::map($rawData,"ref","sname");

        $Sql = 'SELECT changwatcode, concat(changwatname," - ",changwatcode) as changwatname '
                .' FROM mophrefer.cchangwat ORDER BY changwatname ';
        $rawData = Yii::$app->db_admin->createCommand($Sql)->queryAll();
        $changwatName = yii\helpers\ArrayHelper::map($rawData,"changwatcode","changwatname");

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id,'referName'=>$referName]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'referName'=>$referName,
                'changwatName'=>$changwatName,
            ]);
        }
    }

    /**
     * Deletes an existing ReferProvider model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ReferProvider model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReferProvider the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReferProvider::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
