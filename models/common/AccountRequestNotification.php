<?php

namespace humanized\user\models\common;

use humanized\user\models\common\User;
use yii\base\Model;

/**
 * Password reset request form
 */
class AccountRequestNotification extends Model
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
        $admins = User::findAll(['enable_notifications' => TRUE]);

        foreach ($admins as $admin) {
            \Yii::$app->mailer->compose(['html' => '@vendor/humanized/yii2-user/mail/accountRequestNotification-html', 'text' => '@vendor/humanized/yii2-user/mail/accountRequestNotification-text'], ['account' => $account])
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                    ->setTo($admin->email)
                    ->setSubject('Account request pending approval ' . \Yii::$app->name)
                    ->send();
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
