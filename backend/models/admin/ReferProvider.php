<?php

namespace app\models\admin;

use Yii;

/**
 * This is the model class for table "refer_provider".
 *
 * @property integer $id
 * @property string $prov
 * @property string $region
 * @property string $provider
 * @property string $date_register
 * @property string $date_expire
 * @property string $usage_group
 * @property string $api_key
 * @property string $secret_key
 * @property string $secret_default
 * @property string $hashing
 * @property string $responder
 * @property string $tel
 * @property string $lastkeychange
 * @property string $lastlogin
 * @property string $remark
 * @property string $lastupdate
 */
class ReferProvider extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'refer_provider';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_admin');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['prov', 'region', 'provider', 'secret_key', 'secret_default', 'hashing' , 'responder', 'tel'], 'required'],
            [['date_register', 'date_expire', 'lastkeychange', 'lastlogin', 'lastupdate'], 'safe'],
            [['usage_group', 'api_key', 'remark'], 'string'],
            [['prov'], 'string', 'max' => 2],
            [['region'], 'string', 'max' => 4],
            [['provider'], 'string', 'max' => 200],
            [['secret_key', 'secret_default' , 'hashing'], 'string', 'min' => 6],
            [['secret_key', 'secret_default'], 'string', 'max' => 128],
            [['hashing'], 'string', 'max' => 10],
            [['responder'], 'string', 'max' => 50],
            [['tel'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'prov' => Yii::t('app', 'จังหวัด'),
            'region' => Yii::t('app', 'เขต'),
            'provider' => Yii::t('app', 'ชื่อระบบ Refer'),
            'date_register' => Yii::t('app', 'Date Register'),
            'date_expire' => Yii::t('app', 'Date Expire'),
            'usage_group' => Yii::t('app', 'ระดับ Server'),
            'api_key' => Yii::t('app', 'API Key'),
            'secret_key' => Yii::t('app', 'Secret Key'),
            'secret_default' => Yii::t('app', 'Secret Default'),
            'hashing' => Yii::t('app', 'Hashing'),
            'responder' => Yii::t('app', 'ผู้ประสานงาน'),
            'tel' => Yii::t('app', 'โทร.'),
            'lastkeychange' => Yii::t('app', 'Lastkeychange'),
            'lastlogin' => Yii::t('app', 'Lastlogin'),
            'remark' => Yii::t('app', 'หมายเหตุ'),
            'lastupdate' => Yii::t('app', 'Lastupdate'),
        ];
    }

    /**
     * @inheritdoc
     * @return ReferProviderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ReferProviderQuery(get_called_class());
    }
}
