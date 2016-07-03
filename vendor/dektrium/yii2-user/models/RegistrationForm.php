<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\models;

use dektrium\user\traits\ModuleTrait;
use Yii;
use yii\base\Model;

/**
 * Registration form collects user input on registration process, validates it and creates new User model.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RegistrationForm extends Model
{
    use ModuleTrait;
    /**
     * @var string User email address
     */
    public $email;

    /**
     * @var string Username
     */
    public $username;
    public $prename;
    public $fname;
    public $mname;
    public $lname;
    public $position;
    public $position_level;
    public $tel_office;
    public $tel_mobile;
    public $remark;
    public $hcode;

    /**
     * @var string Password
     */
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $user = $this->module->modelMap['User'];

        /*
                    * @property string  $prename
                    * @property string  $fname
                    * @property string  $mname
                    * @property string  $lname
                    * @property string  $position
                    * @property string  $position_level
                    * @property string  $tel_office
                    * @property string  $tel_mobile
                    * @property string  $remark
            */
        return [
            // username rules
            'nameLength'   => [['fname','lname'], 'string', 'min' => 2, 'max' => 50],
            'lnameLength'   => [['prename','position'], 'string', 'min' => 3, 'max' => 50],
            'hcodeLength'   => [['hcode'], 'string', 'min' => 5, 'max' => 50],
            'nameRequired' => [['prename','fname','lname','position','hcode','tel_mobile'], 'required'],
            'nameTrim'     => [['prename','fname','lname','position','position_level','hcode','tel_office','tel_mobile','remark'], 'filter', 'filter' => 'trim'],
            'telLength'   => ['tel_mobile', 'string', 'min' => 9, 'max' => 20],
            'usernameLength'   => ['username', 'string', 'min' => 4, 'max' => 20],
            'usernameTrim'     => ['username', 'filter', 'filter' => 'trim'],
            'usernamePattern'  => ['username', 'match', 'pattern' => $user::$usernameRegexp],
            'usernameRequired' => ['username', 'required'],
            'usernameUnique'   => [
                'username',
                'unique',
                'targetClass' => $user,
                'message' => Yii::t('user', 'This username has already been taken')
            ],
            // email rules
            'emailTrim'     => ['email', 'filter', 'filter' => 'trim'],
            'emailRequired' => ['email', 'required'],
            'emailPattern'  => ['email', 'email'],
            'emailUnique'   => [
                'email',
                'unique',
                'targetClass' => $user,
                'message' => Yii::t('user', 'This email address has already been taken')
            ],
            // password rules
            'passwordRequired' => ['password', 'required', 'skipOnEmpty' => $this->module->enableGeneratingPassword],
            'passwordLength'   => ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'prename'          => Yii::t('user', 'คำนำหน้า'),
            'fname'          => Yii::t('user', 'ชื่อ'),
            'mname'          => Yii::t('user', 'ชื่อกลาง'),
            'lname'          => Yii::t('user', 'นามสกุล'),
            'position'          => Yii::t('user', 'ตำแหน่ง'),
            'position_level'          => Yii::t('user', 'ระดับ'),
            'hcode'            => Yii::t('user','สถานพยาบาล'),
            'tel_office'          => Yii::t('user', 'โทร(ที่ทำงาน)'),
            'tel_mobile'          => Yii::t('user', 'มือถือ'),
            'remark'          => Yii::t('user', 'หมายเหตุ'),
            'email'    => Yii::t('user', 'Email'),
            'username' => Yii::t('user', 'Username'),
            'password' => Yii::t('user', 'Password'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'register-form';
    }

    /**
     * Registers a new user account. If registration was successful it will set flash message.
     *
     * @return bool
     */
    public function register()
    {
        if (!$this->validate()) {
            return false;
        }

        /** @var User $user */
        $user = Yii::createObject(User::className());
        $user->setScenario('register');
        $this->loadAttributes($user);

        if (!$user->register()) {
            return false;
        }

        Yii::$app->session->setFlash(
            'info',
            Yii::t('user', 'Your account has been created and a message with further instructions has been sent to your email')
        );

        return true;
    }

    /**
     * Loads attributes to the user model. You should override this method if you are going to add new fields to the
     * registration form. You can read more in special guide.
     *
     * By default this method set all attributes of this model to the attributes of User model, so you should properly
     * configure safe attributes of your User model.
     *
     * @param User $user
     */
    protected function loadAttributes(User $user)
    {
        $user->setAttributes($this->attributes);
    }
}
