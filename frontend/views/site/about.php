<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = Yii::$app->params["siteName"].'  V.'.Yii::$app->params["version"];
$this->params['breadcrumbs'][] = Yii::$app->Udf->thDateAbbr();

?>
<div class="site-about">
<?php
$indicesServer = [
    'PHP_SELF',
    'GATEWAY_INTERFACE',
    'SERVER_NAME',
    'SERVER_SOFTWARE',
    'SERVER_PROTOCOL',
    'REQUEST_METHOD',
    'REQUEST_TIME',
    'REQUEST_TIME_FLOAT',
    'QUERY_STRING',
    'HTTP_ACCEPT',
    'HTTP_ACCEPT_CHARSET',
    'HTTP_ACCEPT_ENCODING',
    'HTTP_ACCEPT_LANGUAGE',
    'HTTP_CONNECTION',
    'HTTP_HOST',
    'HTTP_REFERER',
    'HTTP_USER_AGENT',
    'HTTPS',
    'REMOTE_ADDR',
    'REMOTE_HOST',
    'REMOTE_PORT',
    'REMOTE_USER',
    'REDIRECT_REMOTE_USER',
    'SERVER_ADMIN',
    'SERVER_PORT',
    'SERVER_SIGNATURE',
    'PATH_TRANSLATED',
    'SCRIPT_NAME',
    'REQUEST_URI',
    'PHP_AUTH_DIGEST',
    'PHP_AUTH_USER',
    'PHP_AUTH_PW',
    'AUTH_TYPE',
    'PATH_INFO',
    'ORIG_PATH_INFO'] ;

echo '<h3 class="text-info">Server/Client information</h3>';
echo '<table class="table table-hover table-striped">' ;
echo '<tr><td>SITE NAME</td><td>'.Yii::$app->params["siteName"].'</td></tr>' ;
echo '<tr><td>VERSION</td><td>'.Yii::$app->params["version"].'</td></tr>' ;
foreach ($indicesServer as $arg) {
    if (isset($_SERVER[$arg])) {
        echo '<tr><td>'.$arg.'</td><td>' . $_SERVER[$arg] . '</td></tr>' ;
    }
    else {
        echo '<tr><td>'.$arg.'</td><td>-</td></tr>' ;
    }
}
echo '</table>' ;
?>
</div>
