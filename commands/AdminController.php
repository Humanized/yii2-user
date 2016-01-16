<?php

namespace humanized\user\commands;

use humanized\clihelpers\controllers\Controller;
use humanized\user\models\User;
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

    public function __construct($id, $module, $config = array())
    {
        parent::__construct($id, $module, $config);
        $this->_userClass = \Yii::$app->user->identityClass;
    }

    public function actionIndex()
    {
        echo "Welcome to the Humanized User Administrator CLI \n";
        echo "This tool requires Yii 2.0.7 or later \n";
        return 0;
    }

    public function actionDelete($email)
    {
        $exitCode = 0;
        $this->stdout("Deleting $email: ");
        $deleteCounter = User::deleteAll(['email' => $email]);
        if (1 === $deleteCounter) {
            $this->msgSuccess();
        } else {


            //Error Handling

            if ($deleteCounter === 0) {
                $exitCode = 1;
                $this->msgError("Account Not Found");
            } else {
                $this->msgError("Multiple Accounts Deleted - DB may be in Inconsistent State");
                $this->stderr("Multiple Accounts Deleted - DB may be in Inconsistent State", Console::BG_BLUE);
                $exitCode = 2;
            }
        }
        $this->stdout("\n");
        return $exitCode;
    }

    public function actionSendPasswordResetLink($email)
    {
        $this->stdout("\nSending Password Reset Link to User: ");
        $this->sendMail($email);
    }

    public function actionSendAccessTokenLink($email)
    {
        
    }

    private function sendMail($email)
    {
        $model = new PasswordReset(['email' => $email]);
        if ($model->validate() && $model->sendEmail()) {
            $this->stdout("OK", Console::FG_GREEN, Console::BOLD);
        } else {
            $this->stdout("FAILED", Console::FG_RED, Console::BOLD);
            $this->stderr("\nGenerated Message: ");
            $this->stderr('System Unable to reset password for the email provided', Console::BG_BLUE);
        }
    }

    /**
     * Add a user account to the system.
     * 
     * Upon submitting a valid username/email combination, a prompt is launched to get the password corresponding to the user-account. If no password is provided, the system will email the created. 
     * 
     * @todo Optional full exception output
     * @todo Validate e-mail format
     * @todo Implement stty echo alternative for windows (for now windows is not supported)
     * @param type $email Unique E-mail address to be assigned to the user account (mandatory)
     * @param type $user  Unique username to be assigned to the user account (optional) - If no username is provided, the email address is used.
     * @return int 0 for success, 1 for save error
     */
    public function actionAdd($email, $user = NULL)
    {
        $this->stdout("Adding Account for $email");
        $exitCode = 0;
        //Creates User model and sets provided variables
        $this->setupModel($email, $user);
        //Prompt for account password
        $sendPasswordResetMail = $this->promptPassword();
        //Save the model
        try {
            if (!$this->_model->save()) {
                $this->stdout("FAILED", Console::FG_RED, Console::BOLD);
                $this->stderr("\nGenerated Message: ");
                //Todo: Optional full error message display
                $this->stderr('Unable to save to Database - Validator Failed', Console::BG_BLUE);
            } elseif ($sendPasswordResetMail) {
                $this->actionSendPasswordResetLink($this->_model->email);
            }
        } catch (\Exception $e) {
            $this->stdout("FAILED", Console::FG_RED);
            $this->stderr("\nGenerated Message: ");
            //Todo: Optional full error message display
            $this->stderr(strtok($e->getMessage(), "\n"), Console::BG_BLUE);
            $exitCode = 1;
        }
        //Should remove in stable versions, but nice little fallback just in case
        $this->unhideUserInput();
        //Two newlines B4 program exit
        $this->stdout("\n\n");
        return $exitCode;
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
        $exitCode = FALSE;
        $this->hideUserInput();
        $passwd = $this->_promptPassword();
        $this->stdout("OK", Console::FG_GREEN, Console::BOLD);
        $confirm = "";
        if ($passwd !== "") {
            $confirm = $this->_promptPassword(TRUE);
        }
        //Restart when passwords do not match OR rejected confirmation
        //Should remove in stable versions, but nice little fallback just in case
        $this->unhideUserInput();
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

    /**
     * @todo Move to a CLI Helper Class
     * @todo Make compatible with Windows (stty alternative)
     */
    private function hideUserInput()
    {
        if (!\yii\helpers\Console::isRunningOnWindows()) {
            system('stty -echo');
        }
    }

    /**
     * @todo Move to a CLI Helper Class
     * @todo Make compatible with Windows (stty alternative)
     */
    private function unhideUserInput()
    {
        if (!\yii\helpers\Console::isRunningOnWindows()) {
            system('stty echo');
        }
    }

}
