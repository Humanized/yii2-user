<?php

namespace humanized\user\commands;

use humanized\clihelpers\controllers\Controller;
use humanized\user\models\common\User;
use humanized\user\models\common\PasswordResetRequest;
use yii\helpers\Console;

/**
 * A CLI allowing basic Yii2 user administration functions.
 * 
 * The interface requires that proper configuration of the user component to reference the identity class for user management operations 
 * along with the optional setup of the authmanager component to allow user role assignment
 * 
 * Accounts are referenced, through a unique email address or username.
 * 
 * The email address is required. The username is considered optional, and following cases are to be considered:
 * 
 * <ul>
 * <li>Identity Model does not have a username attribute: email address is used to reference user-account </li>
 * <li>Identity Model has username attribute: If unset, the email address will be used as username. No username to reference accounts</li>
 * </ul>
 * 
 * As such, all commands provided take following parameters:
 *  
 * <table>
 * <tr><td>email</td><td>Account email specification</td></tr>
 * <tr><td>username</td><td>Account username specification</td></tr>
 * </table>
 * 
 * 
 * Using general configuration specified above, i.e. the use of any class implementing the IdentityInterface, following commands are supported:
 * 
 * <table>
 * <tr><td><module-name>/admin/create</td><td>role-name(optional)</td><td></td></tr>
 * <tr><td><module-name>/admin/delete</td><td></td><td></td></tr>
 * <tr><td><module-name>/admin/set-status</td><td>status-code(required)</td><td></td></tr>
 * </table>
 * 
 * When the identity class is instance of, or inherits methods provided by the humanized/user/common/User model, additional commands are available
 * 
 * <table>
 * <tr><td><module-name>/admin/send-password-reset-link</td><td></td><td></td></tr>
 * <tr><td><module-name>/admin/activate-account</td><td></td><td></td></tr>
 * <tr><td><module-name>/admin/disactivate-account</td><td></td><td></td></tr>
 * </table>
 * 
 * 
 * @name User Administration CLI
 * @version 0.1
 * @author Jeffrey Geyssens <jeffrey@humanized.be>
 * @package yii2-user
 *
 */
class AdminController extends Controller
{

    public $role;
    public $email;
    public $username;
    private $_user;
    private $_auth;

    public function __construct($id, $module, $config = array())
    {
        parent::__construct($id, $module, $config);
        //Require existence of identityClass and private object instantiation
        $userClass = \Yii::$app->user->identityClass;
        //Throw error if unset
        $this->_user = new $userClass();
        //Optional existence of authManager class and private object instantiation
        if (isset(\Yii::$app->authManager)) {
            $this->_auth = \Yii::$app->authManager;
        }
    }

    private function findModel($user)
    {
        $model = $this->_user;
        $isEmail = filter_var($user, FILTER_VALIDATE_EMAIL);

        if (!$model->hasAttribute('username') && !$isEmail) {
            return NULL;
        }
        return !$model::findOne([($isEmail ? 'email' : 'username') => $user]);
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
                $this->_msg = "Unable to Delete User Account linked to $user";
            } catch (\Exception $e) {
                $this->_exitCode = 200;
                $this->_msg = $e->getMessage();
            }
        } else {
            $this->_exitCode = 100;
            $this->_msg = "Could not find account linked to $user";
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
        $model = new PasswordResetRequest(['email' => $email]);
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
        if (!$this->_setupModel($email, $user)) {
            return $this->_exitCode;
        }

        //Prompt for account password
        $sendMail = $this->_prompt();
        //Save the model
        try {
            //Set status to active
            $this->_user->status = 10;
            if (!$this->_user->save()) {
                \yii\helpers\VarDumper::dump($this->_user->getErrors());
                $this->_exitCode = 101;
                $this->_msg = 'Model Save ERROR';
            }
        } catch (\Exception $e) {
            $this->_exitCode = $e->getCode();
            $this->_msg = $e->getMessage();
        }
        if ($this->_exitCode === 0) {
            $this->_msg = 'Account created with e-mail address: ' . $email;
            if ($sendMail) {
                $this->_msg.= ' - Confirmation mail sent';
            }
        }

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
    private function _setupModel($email, $user)
    {
        $this->_user->email = $email;
        if (!$this->_user->validate(['email'])) {
            $this->_msg = $this->_user->errors['email'][0];
            $this->_exitCode = 101;
        }
        if ($this->_exitCode === 0 && $this->_user->hasAttribute('username')) {
            $this->_user->username = (isset($user) ? $user : $email);
            if (!$this->_uer->validate(['username'])) {
                $this->_msg = $this->_user->errors['username'][0];
                $this->_exitCode = 102;
            }
        }
        return 0 === $this->_exitCode;
    }

    private function _prompt()
    {
        $this->hideInput();
        $passwd = $this->_promptPassword();
        $this->stdout("OK", Console::FG_GREEN, Console::BOLD);

        $confirm = "";
        if ($passwd !== "") {
            $confirm = $this->_promptPassword(TRUE);
        }

        $this->showInput();
        //Restart when passwords do not match OR rejected confirmation
        if (($passwd !== $confirm) || ($passwd === "" && !$this->confirm("\nGenerate password and send confirmation mail?"))) {
            return $this->_promptPassword();
        }

        //Autogenerate password
        if ($passwd === "") {
            $this->_user->generatePassword = TRUE;
        }
        if (!$this->_user->generatePassword) {
            $this->_user->password = $passwd;
            $this->_user->password_confirm = $confirm;
        }

        return $this->_user->generatePassword;
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
