<?php
/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
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
        'brandLabel' => '<img src="/images/moph.png" width="27">',
//        'brandLabel' => Yii::$app->params["siteName"],
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-nrefer navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        //['label' => 'Home', 'url' => ['/site/index']],
        ['label' => 'รายงาน', 'items'=>[
            ['label' => 'สรุปรายปีงบประมาณ', 'url' => ['/report']],
            '<li class="divider"></li>',
            ['label' => 'สรุปตามช่วงวันที่'],
            ['label' => 'รายเขต', 'url' => ['/refer/report/sum_region?date='.date("Y-m-d")]],
            ['label' => 'รายจังหวัด', 'url' => ['/refer/report/sum_prov?date='.date("Y-m-d")]],
            ['label' => 'ราย รพ.', 'url' => ['/refer/report/sum_hcode?date='.date("Y-m-d")]],
        ]],
        ['label' => 'About', 'url' => ['/site/about']],
        Yii::$app->user->isGuest ?
        ['label' => 'Sign in', 'url' => ['/user/security/login']] :
        ['label' => 'User (' . Yii::$app->user->identity->username . ')', 'items'=>[
            ['label' => 'Profile', 'url' => ['/user/settings/profile']],
            ['label' => 'Account', 'url' => ['/user/settings/account']],
//            '<li class="divider"></li>',
//            ['label' => 'User Admin', 'url' => ['/user/admin/index']],
//            ['label' => 'Refer Provider', 'url' => ['/admin/referprovider']],
            '<li class="divider"></li>',
            ['label' => 'Logout', 'url' => ['/user/security/logout'],'linkOptions' => ['data-method' => 'post']],
        ]],
//        ['label' => 'Register', 'url' => ['/user/registration/register'], 'visible' => Yii::$app->user->isGuest],
    ];
    echo Nav::widget([
               'options' => ['class' => 'navbar-nav navbar-left'],
               'encodeLabels' => false,
               'items' => [['label' => Yii::$app->params["siteName"],'url'=>'/site/index']],
           ]);
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
