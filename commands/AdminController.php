<?php

namespace humanized\user\commands;

use humanized\clihelpers\controllers\Controller;
use humanized\user\models\PasswordReset;
use yii\helpers\Console;

/**
 * A CLI allowing basic Yii2 user administration functions.
 * 
 * Supported commands:
 * 
 * > user/admin/create <email:required> <uname:optional>
 * 
 * > user/admin/delete <email:required>
 * 
 * > user/admin/send-password-reset-link <email:required>
 * 
 * > user/admin/send-token-generation-link <email:required>
 * 
 * 
 * @name User Administration CLI
 * @version 0.1
 * @author Jeffrey Geyssens <jeffrey@humanized.be>
 * @package yii2-user
 * 
 * 
 * 
 */
class AdminController extends Controller {

    private $_model;
    private $_userClass;
    private $_findUser;

    public function __construct($id, $module, $config = array())
    {
        parent::__construct($id, $module, $config);
        $this->_userClass = $module->params['identityClass'];
        $this->_findUser = $module->params['fnUser'];
    }

    private function findModel($user)
    {
        $userClass = $this->_userClass;
        $fn = $this->_findUser;
        return $userClass::$fn($user);
    }

    public function actionIndex()
    {
        echo "Welcome to the Humanized User Administrator CLI \n";
        echo "This tool requires Yii 2.0.7 or later \n";
        return 0;
    }

    /**
     * Deletes a user account from the system.
     * 
     * @param string $user
     * @return int Status-Code
     */
    public function actionDelete($user)
    {
        $model = $this->findModel($user);
        if (isset($model)) {
            try {
                if ($model->delete()) {
                    $this->_msg = "User Account linked to $user Successfully Deleted";
                }
            } catch (\Exception $e) {
                $this->_exitCode = 200;
                $this->_msg = $e->getMessage();
            }
        } else {
            $this->_exitCode = 100;
            $this->_msg = "User Account linked to $user Not Found";
        }

        $this->exitMsg();
        return $this->_exitCode;
    }

    public function actionSendPasswordResetLink($email)
    {
        $this->sendMail($email);
    }

    public function actionSendAccessTokenLink($email)
    {
        
    }

    private function sendMail($email)
    {
        $model = new PasswordReset(['email' => $email]);
        if ($model->validate() && $model->sendEmail()) {
            $this->_msg = 'Password reset link successfully sent to ' . $email;
        } else {
            $this->_exitCode = 400;
            $this->_msg = 'Password reset link could not be sent to ' . $email;
        }
    }

    /**
     * Add a user account to the system.
     * 
     * Upon submitting a valid username/email combination, a prompt is launched to set account password. 
     * If no password is provided, the system will generate a strong password automatically and send a mail to the user account email
     * requiring a password reset in order to activate the account. 
     * 
     * This implementation considers the email address as mandatory and the username as optional.
     * If no user-name is provided, the username will be set to the email adress enforcing uniquess on both.
     * 
     * 
     * 
     * @todo Optional full exception output
     * @todo Validate e-mail format
     * @todo Implement stty echo alternative for windows (for now windows is not supported)
     * @param type $email Unique E-mail address to be assigned to the user account (mandatory)
     * @param type $user  Unique username to be assigned to the user account (optional) - If no username is provided, the email address is used.
     * @return int 0 for success, 1 for save error
     */
    public function actionCreate($email, $user = NULL)
    {
        //Creates User model and sets provided variables
        $this->setupModel($email, $user);
        //Prompt for account password
        $sendPasswordResetMail = $this->promptPassword();
        //Save the model
        try {
            if (!$this->_model->save()) {
                $this->_exitCode = 100;
                $this->_msg = 'Unable to save to Database - Validator Failed';
            } elseif ($sendPasswordResetMail) {
                $this->actionSendPasswordResetLink($this->_model->email);
            }
        } catch (\Exception $e) {
            $this->_exitCode = 200;
            $this->_msg = $e->getMessage();
        }
        //Should remove in stable versions, but nice little fallback just in case
        $this->showInput();
        //Two newlines B4 program exit
        $this->exitMsg();
        return $this->_exitCode;
    }

    /**
     * Initialises User model, by setting email and username combination
     * along with generating an random authentication key
     * 
     * @param string $email
     * @param string $user
     * @return User
     */
    private function setupModel($email, $user)
    {
        $this->_model = new $this->_userClass([
            'email' => $email,
            'auth_key' => \Yii::$app->security->generateRandomString(),
            'username' => (isset($user) ? $user : $email)
        ]);
    }

    private function promptPassword()
    {
        $this->hideInput();
        $passwd = $this->_promptPassword();
        $this->stdout("OK", Console::FG_GREEN, Console::BOLD);
        $confirm = "";
        if ($passwd !== "") {
            $confirm = $this->_promptPassword(TRUE);
        }
        //Restart when passwords do not match OR rejected confirmation
        //Should remove in stable versions, but nice little fallback just in case
        $this->showInput();
        if (($passwd !== $confirm) || ($passwd === "" && !$this->confirm("\nGenerate Password Automatically?"))) {
            return $this->promptPassword();
        }
        if ($passwd === "") {
            $passwd = \Yii::$app->security->generateRandomString();
            $exitCode = TRUE;
        }
        $this->_model->password = $passwd;
        return $exitCode;
    }

    /**
     * Private function that prompts for and, validates CLI provided user account passwords.
     *  
     * @param bool $confirm - displays Confirmation message when true
     * @return string - returns the password when once valid password is provided
     */
    private function _promptPassword($confirm = FALSE)
    {
        $passwd = false;
        while (false === $passwd) {
            $passwd = $this->prompt("\n" . ($confirm ? "Confirm" : "Submit") . " User Account Password: ");
        }
        return $passwd;
    }

}
