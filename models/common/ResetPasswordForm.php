<?php

namespace humanized\user\models\common;

use yii\base\Model;
use yii\base\InvalidParamException;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{

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
     * @var \common\models\User
     */
    private $_user;

    /**
     * Creates a form model given a token.
     *
     * @param  string                          $token
     * @param  array                           $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        $identityClass = \Yii::$app->user->identityClass;
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Password reset token cannot be blank.');
        }
        $this->_user = $identityClass::findByPasswordResetToken($token);
        if (!$this->_user) {
            throw new InvalidParamException('Wrong password reset token.');
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'string', 'min' => 8],
            ['password', 'required'],
            ['password_confirm', 'required'],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'message' => "Passwords don't match"],
        ];
    }

    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->generatePassword = FALSE;
        $user->removePasswordResetToken();

        return $user->save(false);
    }

}
