<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

// cosmo , cyborg , cerulean , darkly , lumen , flatly , readable , slate , spacelab , united , sandstone
//raoul2000\bootswatch\BootswatchAsset::$theme = 'cerulean';
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode(Yii::$app->params["siteName"]) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Admin@nRefer',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-nrefer navbar-fixed-top',
        ],
    ]);
    $menuItems = [
    //    ['label' => 'Home', 'url' => ['/site/index']],
        ['label' => 'Frontend', 'url' => Url::to('http://nrefer.moph.go.th')],
        !Yii::$app->user->isGuest ?
        ['label' => 'Monitoring', 'items'=>[
            ['label' => 'Request Logging', 'url' => ['/report/log']],
        ]]:'',
        !Yii::$app->user->isGuest ?
        ['label' => 'Admin', 'items'=>[
            ['label' => 'User Admin', 'url' => ['/user/admin/index']],
            ['label' => 'Refer Provider', 'url' => ['/admin/referprovider']],
        ]]:'',
        Yii::$app->user->isGuest ?
        ['label' => 'Sign in', 'url' => ['/user/security/login']] :
        ['label' => 'User (' . Yii::$app->user->identity->username . ')', 'items'=>[
            ['label' => 'Profile', 'url' => ['/user/settings/profile']],
            ['label' => 'Account', 'url' => ['/user/settings/account']],
            '<li class="divider"></li>',
            ['label' => 'Logout', 'url' => ['/user/security/logout'],'linkOptions' => ['data-method' => 'post']],
        ]],
    ];

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?=Yii::$app->params["siteName"]?> 2015. v<?=Yii::$app->params["version"]?> </p>

        <p class="pull-right">Powered by <strong>InspireTeam@MOPH</p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
