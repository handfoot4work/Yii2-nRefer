<?php

/* @var $this yii\web\View */

$this->title = Yii::$app->params["siteName"];
?>
<div class="site-index">

    <div class="jumbotron">
        <p class="text-center"><img src="images/moph.png" width="120" border="0" /></p>
        <h3 class="text-primary"><?=Yii::$app->params["siteName"]?> <?=Yii::$app->params["siteDesc"]?></h3>
        <p class="lead"><br>
            <strong>วิสัยทัศน์</strong><br>
            "<?=Yii::$app->params["officeVision"]?>"</p>
        <p class="text-left">
            <strong>พันธกิจ</strong>
            <ol class="text-left">
                <li>กำหนดนโยบาย มาตรฐาน กฎหมาย และบริหารจัดการบนฐานข้อมูลที่มีคุณภาพและการจัดการความรู้รวมถึงการติดตามกำกับประเมินผล (Regulator)</li>
                <li>จัดระบบบริการตั้งแต่ระดับปฐมภูมิจนถึงบริการศูนย์ความเป็นเลิศที่มีคุณภาพ ครอบคลุม และ<b class="text-danger">ระบบส่งต่อที่ไร้รอยต่อ (Provider)</b></li>
            </ol>
        </p>
    </div>

    <div class="body-content">


    </div>
</div>
