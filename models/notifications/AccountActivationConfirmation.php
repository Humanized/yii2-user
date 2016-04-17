<?php

namespace humanized\user\models\notifications;

use humanized\user\models\common\User;
use yii\base\Model;

/**
 * Password reset request form
 */
class AccountActivationConfirmation extends Model
{

    public $email;

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $account = User::findOne([
                    //  'status' => User::STATUS_ACTIVE,
                    'email' => $this->email,
        ]);

        if (!isset($account)) {
            return false;
        }
        \Yii::$app->mailer->compose(['html' => '@vendor/humanized/yii2-user/mail/accountActivationConfirmation-html', 'text' => '@vendor/humanized/yii2-user/mail/accountActivationConfirmation-text'], ['account' => $account])
                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                ->setTo($account->email)
                ->setSubject('Account request pending approval ' . \Yii::$app->name)
                ->send();

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
