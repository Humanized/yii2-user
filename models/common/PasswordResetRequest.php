<?php

namespace humanized\user\models\common;

use humanized\user\models\common\User;
use yii\base\Model;

/**
 * Password reset request form
 */
class PasswordResetRequest extends Model
{

    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\humanized\user\models\common\User',
                //      'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'There is no user with such email.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
                    //  'status' => User::STATUS_ACTIVE,
                    'email' => $this->email,
        ]);
        if ($user) {
            $user->setScenario(User::SCENARIO_PWDRST);
            if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->generatePasswordResetToken();
            }

            if ($user->save()) {
                return \Yii::$app->mailer->compose(['html' => '@vendor/humanized/yii2-user/mail/passwordResetToken-html', 'text' => '@vendor/humanized/yii2-user/mail/passwordResetToken-text'], ['user' => $user])
                                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                                ->setTo($this->email)
                                ->setSubject('Password reset for ' . \Yii::$app->name)
                                ->send();
            }
        }

        return true;
    }

    public function loadMail($id)
    {
        $user = User::findOne($id);
        if (!isset($user)) {
            return FALSE;
        }
        $this->email = $user->email;
        return TRUE;
    }

}
