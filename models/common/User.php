<?php

namespace humanized\user\models\common;

use Yii;
use humanized\user\models\common\PasswordResetRequest;
use humanized\user\models\common\AuthenticationToken;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * 
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface {

    /**
     * Supported model scenarios
     */
    const SCENARIO_ADMIN = 'admin';
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_SIGNUP = 'signup';
    const SCENARIO_PWDRST = 'password-reset';

    /**
     * Default model status modes
     */
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 10;

    /**
     *
     * @var boolean 
     */
    public $generatePassword = FALSE;

    /**
     *
     * @var string 
     */
    public $password;

    /**
     * 
     * @var string 
     */
    public $password_confirm;

    /**
     *
     * @var array<string> List of user roles 
     */
    public $roles = [];

    /**
     * Current Module Instance
     * 
     * @var type 
     */
    private $_module = NULL;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->_module = \humanized\user\Module::getInstance();
        if ($this->getScenario() == self::SCENARIO_ADMIN) {
            $this->generatePassword = TRUE;
            if ($this->_module->params['enableStatusCodes']) {
                $this->status = $this->_module->params['defaultStatusCode'];
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * Module considers email over username as predominant lookup value.
     * Status Range is setup to configuration settings
     * 
     */
    public function rules()
    {
        $rules = [
            ['email', 'required'],
            ['email', 'unique'],
            ['email', 'email'],
        ];
        if (\Yii::$app->controller->module->params['enableUserName']) {
            $rules[] = ['username', 'unique'];
        }
        if (\Yii::$app->controller->module->params['enablePasswords']) {
            $this->appendPasswordRules($rules);
        }
        if ($this->_module->params['enableStatusCodes']) {

            $rules[] = ['status', 'default', 'value' => ($this->_module->params['enableUserVerification'] || $this->_module->params['enableAdminVerification']) ? self::STATUS_ACTIVE : self::STATUS_INACTIVE];
            $rules[] = ['status', 'in', 'range' => array_keys(\Yii::$app->controller->module->params['statusCodes'])];
        }
        if ($this->_module->params['enableRBAC']) {
            $rules[] = ['roles', 'each', 'rule' => ['range' => array_keys(\Yii::$app->authManager->getRoles())]];
        }
        return $rules;
    }

    public function appendPasswordRules(&$rules)
    {
        $when = function($model) {
            return !$model->generatePassword;
        };
        $whenClient = "function (attribute, value) {
                                    return $('#generate-password').checked==false;
                                }";
        $rules = array_merge($rules, [
            ['generatePassword', 'required', 'on' => [self::SCENARIO_ADMIN]],
            ['password', 'string', 'min' => 8],
            ['password', 'required',
                'when' => $when,
                'whenClient' => $whenClient
            ],
            ['password_confirm', 'required',
                'on' => [self::SCENARIO_SIGNUP, self::SCENARIO_ADMIN],
                'when' => $when,
                'whenClient' => $whenClient
            ],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'message' => "Passwords don't match"],
        ]);
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_SIGNUP] = ['email', 'password', 'password_confirm'];
        $scenarios[self::SCENARIO_PWDRST] = [];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $model = AuthenticationToken::findOne(['token_hash' => $token]);

        return isset($model) ? $model->user : NULL;
    }

    /**
     * Finds user by username or email
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {

        $isEmail = filter_var($username, FILTER_VALIDATE_EMAIL);

        //BAHHHH    
        if (!\humanized\user\Module::getInstance()->params['enableUserName'] && !$isEmail) {
            return NULL;
        }

        //    var_dump([($isEmail ? 'email' : 'username') => $username]);
        return static::findOne([($isEmail ? 'email' : 'username') => $username]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
                    'password_reset_token' => $token,
                    'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Extra steps are taken in scenarios where user account password are handled.
     * 
     * 
     * @return type
     */
    public function beforeValidate()
    {
        if ($this->scenario == self::SCENARIO_ADMIN || $this->scenario == self::SCENARIO_SIGNUP) {
            $this->generateAuthKey();
            $this->_generatePassword();
        }
        return parent::beforeValidate();
    }

    /**
     * Extra steps are taken in scenarios where user account password are handled
     * 
     * 
     * @return type
     */
    public function afterSave($insert, $changedAttributes)
    {
        $cond1 = $this->generatePassword && !$this->_module->params['enableAdminVerification'];
        $cond2 = isset($changedAttributes['status']) && $this->status != 0;
        if ($cond1 || $cond2) {
            $model = new PasswordResetRequest(['email' => $this->email]);
            if (!($model->validate() && $model->sendEmail())) {
                return false;
            }
        }


        return parent::afterSave($insert, $changedAttributes);
    }

    private function _generatePassword()
    {
        $passwd = $this->password;
        if ($this->scenario == self::SCENARIO_SIGNUP) {
            $this->generatePassword = FALSE;
        }
        if ($this->generatePassword) {
            $passwd = \Yii::$app->security->generateRandomString();
        }
        $this->setPassword($passwd);
    }

}
