<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'name'=>'Admin@nRefer',
    'bootstrap' => ['log'],
    'components' => [
        'user' => [
        //    'identityClass' => 'common\models\User',
        //    'enableAutoLogin' => true,
            'identityClass' => 'dektrium\user\models\User',
            'enableAutoLogin' => false,
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
            'class' => 'yii\web\UrlManager',
    	    'enablePrettyUrl' => true,
    	    'showScriptName' => false,
    	    'rules' => array(
        		'<controller:\w+>/<id:\d+>' => '<controller>/view',
        		'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
        		'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
        		'module/<module:\w+>/<controller:\w+>/<action:\w+>' => '<controller>/<action>',
    	    ),
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => '',
        ],
        'assetManager' => [
            'bundles' => [
                'dosamigos\google\maps\MapAsset' => [
                    'options' => [
                        'key' => 'AIzaSyAhV9ttA2I55nbvkumnXag4fqjM11Bx4Zc',// ใส่ API ตรงนี้ครับ
                        //'language' => 'th',
                        'version' => '3.1.18'
                    ]
                ]
            ]
        ],
        'session' => [
            'name' => 'nReferBackend@MOPH',
            //'savePath' => __DIR__ . '/../tmp',
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
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
            // enter optional module parameters below - only if you need to
            // use your own export download action or custom translation
            // message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => []
        ],
        'admin' => [
            'class' => 'app\modules\admin\Module',
        ],
        'ws' => [
            'class' => 'app\modules\ws\Module',
        ],
    ],
    'params' => $params,
];
