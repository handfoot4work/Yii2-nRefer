<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'nReferapp',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'name'=>'nRefer',
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'dektrium\user\models\User',
            'enableAutoLogin' => false,
//            'identityClass' => 'common\models\User',
//            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
        //    'class' => 'yii\web\UrlManager',
    	    'showScriptName' => false,
    	    'enablePrettyUrl' => true,
    	    'rules' => array(
        		'<controller:\w+>/<id:\d+>' => '<controller>/view',
        		'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
        		'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
        		'module/<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
//                ['class' => 'yii\rest\UrlRule', 'controller' => 'refer'],
    	    ),
        ],
    ],
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableUnconfirmedLogin' => false,
            'confirmWithin' => 21600,
            'cost' => 12,
            'admins' => ['admin','pck']
        ],
/*        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['127.0.0.1', '::1', 'nrefer.moph'],
            'password' => '123M@@@456'
        ],
        'debug' => [
    		'class' => 'yii\debug\Module',
    		'allowedIPs' => ['127.0.0.1', '::1']
        ],*/
        'ws' => [
            'class' => 'app\modules\ws\Module',
        ],
        'refer' => [
            'class' => 'app\modules\refer\Module',
        ],
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
            // enter optional module parameters below - only if you need to
            // use your own export download action or custom translation
            // message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => []
        ],
    ],
    'params' => $params,
];
