<?php

namespace humanized\user;

/**
 * User Administration Module for Yii2
 * 
 * This module wraps and extends the basic user management features, provided by the stock advanced Yii2 framework template.
 * 
 * Though implementations vary, the code interface as provided by the advanced template has been ported in it's entirety.
 * 
 * This allows using the module (with it's default settings) to be used as a drop-in replacement for default user managment,
 * without having to worry about breaking code existing code (other than namespace renaming).   
 * 
 *  
 * 
 * GUI
 * 
 * Default user-management  provided by the system
 * 
 * 
 * CLI 
 * 
 * A CLI allowing basic Yii2 user administration functions.
 * 
 * 
 * REST API
 * 
 * Under Construction - Due v0.5
 * 
 * 
 * 
 * @name Yii2 User Administration Module Class 
 * @version 0.1 
 * @author Jeffrey Geyssens <jeffrey@humanized.be>
 * @package yii2-user
 */
class Module extends \yii\base\Module {

    public $identityClass = NULL;
    public $fnUser = NULL;

    /**
     * @since 0.1
     * @var boolean Enable account username attribute. 
     * 
     * When disabled, account email address is used for identification purposes.
     * 
     * Defaults to TRUE
     * 
     * 
     */
    public $enableUserName = TRUE;

    /**
     * @since 0.1
     * @var boolean Enable access token authentication. 
     * 
     * When enabled, authentication tokens can be generated used to authenticate accounts over passwords
     * 
     * Defaults to FALSE
     * 
     */
    public $enableTokenAuthentication = FALSE;

    /**
     * @since 0.1
     * @var boolean Enable public account creation. 
     * 
     * When enabled, the GUI allows visitors to sign-up for an account on the system
     * 
     * Defaults to TRUE
     * 
     */
    public $enableSignUp = TRUE;

    /**
     * @todo Implement this option
     * @var boolean Enable verification of public account creation. 
     * 
     * When enabled, account creation must be confirmed, by clicking the confirmation link sent to the account e-mail address.
     * 
     * Defaults to FALSE
     * 
     */
    public $enableUserVerification = FALSE;

    /**
     * @todo Implement this option
     * @var boolean Enable verification of public account creation by system administrators. 
     * 
     * When enabled, account creation must be confirmed through the user management facilities provided by the module
     * 
     * Defaults to FALSE
     * 
     */
    public $enableAdminVerification = FALSE;

    /**
     * @todo Implement this feature
     * @var boolean Enable password-based login. 
     * 
     * When enabled, users are required to provide a password when logging in to the system 
     * 
     * Defaults to TRUE
     * 
     */
    public $enablePasswords = TRUE;

    /**
     * @todo Implement this option
     * @var boolean Enable PJAX on GUI
     * 
     * When enabled, PJAX is used to provide various usability enchancements.
     * 
     * Defaults to FALSE 
     */
    public $enablePjax = FALSE;

    /**
     * @since 0.1
     * @var boolean Enable account status mechanism 
     * 
     * When enabled, several mechanisms are provided to incorporate user accounts with the default RBAC interface provided by the framework.
     * 
     * Defaults to TRUE    
     */
    public $enableStatusCodes = TRUE;
    public $statusCodeTable = NULL;
    public $statusCodeTableId = 'id';
    public $statusCodeTableName = 'name';
    public $statusCodes = [];
    public $defaultStatusCode = 10;

    /**
     * @since 0.1
     * @var array<mixed> - When enableRBAC is pr 
     */
    public $permissions = [
    ];

    /**
     *
     * @var array<mixed> 
     */
    public $adminFallback = [];

    /**
     * @since 0.1
     * @var boolean Enable role cased authorisation control
     * 
     * When enabled, several mechanisms are provided to incorporate user accounts with the default RBAC interface provided by the framework.
     * 
     * Defaults to FALSE    
     */
    public $enableRBAC = FALSE;
    public $rbacSettings = [];

    public function init()
    {
        parent::init();
        if (\Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'humanized\user\commands';
        }
        $this->initIdentityModel();
        $this->initModuleOptions();
        $this->initGridOptions();
        $this->initStatusCodes();

        //Permission Related initialisation
        $this->initRBAC();
        $this->initPermission();
    }

    private function initIdentityModel()
    {
        //Setting Default Identity class
        if (!isset($this->identityClass)) {
            $this->identityClass = \Yii::$app->user->identityClass;
        }
        //Setting Method to find 
        if (!isset($this->fnUser)) {
            $this->fnUser = "findByUsername";
        }

        $this->params['identityClass'] = $this->identityClass;
        $this->params['fnUser'] = $this->fnUser;
    }

    private function initModuleOptions()
    {
        $this->params['enableTokenAuthentication'] = $this->enableTokenAuthentication;
        $this->params['enableUserName'] = $this->enableUserName;
        $this->params['enableAdminVerification'] = $this->enableAdminVerification;
        $this->params['enableUserVerification'] = $this->enableUserVerification;
        $this->params['enablePasswords'] = $this->enablePasswords;
        $this->params['enableSignUp'] = $this->enableSignUp;
    }

    private function initGridOptions()
    {
        $this->params['enablePjax'] = $this->enablePjax;
        $this->params['enableKartik'] = $this->enablePjax;
        $this->params['enableDynagrid'] = $this->enableRBAC;
    }

    /**
     * 
     * @todo Database Status Code Storage
     */
    private function initStatusCodes()
    {
        $this->params['enableStatusCodes'] = $this->enableStatusCodes;
        if (isset($this->statusCodeTable)) {
            //Load DB values from provided table to the statuscodes variable 
        } elseif (empty($this->statusCodes)) {
            //No Account Status Codes Provided, yet feature is enabled
            //Fallback to stock-like functionality
            $this->params['statusCodes'] = [0 => 'INACTIVE', 10 => 'ACTIVE'];
        }
        $this->params['defaultStatusCode'] = $this->defaultStatusCode;
    }

    /**
     * 
     */
    private function initRBAC()
    {
        $this->params['enableRBAC'] = $this->enableRBAC;
    }

    /**
     * Run after initRBAC method
     */
    private function initPermission()
    {
        $permissions = [
            'accessAdmin' => 'test',
            'assignStatus' => TRUE,
            'assignRole' => TRUE,
            'generateToken' => TRUE,
        ];

        foreach ($this->permissions as $key => $value) {
            $permissions[$key] = $value;
        }
        //array_merge function takes the union and the duplicate keys are overwritten
        $this->params['permissions'] = array_merge($permissions, $this->permissions);
    }

    public function beforeAction($action)
    {
        $accessGranted = TRUE;
        $error = 'Page not found.';



        if (!$accessGranted) {
            throw new \yii\web\NotFoundHttpException($error);
        }
        return $accessGranted && parent::beforeAction($action);
    }

    private function _checkProtected($action, &$accessGranted, &$error)
    {
        $adminAccess = NULL;
        $this->_checkAdminAccess($adminAccess, $error);

        if ($action->id == 'index') {
            if (\Yii::$app->user->isGuest) {
                throw new \yii\web\NotFoundHttpException($error);
            }
            if (\Yii::$app->controller->id == 'admin') {
                $accessGranted = $adminAccess;
            }
        }
    }

    private function _checkAdminAccess(&$accessGranted, &$error)
    {
        $this->_switchPermission($accessGranted, $error);
        if (!isset($accessGranted)) {
            throw new \yii\web\BadRequestHttpException($error);
        }
    }

    private function _switchPermission(&$accessGranted, &$error)
    {
        $accessAdmin = $this->params['permissions']['accessAdmin'];
        switch (gettype($accessAdmin)) {
            case "boolean": {
                    $accessGranted = $accessAdmin;
                    break;
                }
            case "string": {
                    $this->_caseStringPermission($accessGranted, $error);
                    break;
                }
            case "array": {
                    $accessGranted = NULL;
                    break;
                }
            default: {
                    $error = 'Yii2-User: accessAdmin parameter incorrectly set';
                    $accessGranted = NULL;
                    break;
                }
        }
        $this->params['permissions']['accessAdmin'] = $accessGranted;
    }

    private function _caseStringPermission(&$access, &$error)
    {
        $user = \Yii::$app->user;
        $permission = (string) $this->params['permissions']['accessAdmin'];
        $error.= "($user->id::$permission)";

        if (!$this->params['enableRBAC']) {
            $error = 'User Module: RBAC not Enabled';
        }
        $access = $user->can($permission);
    }

}
