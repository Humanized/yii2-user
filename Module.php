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
 * A CLI providing basic Yii2 user management facilties .
 * 
 * 
 * REST API
 * 
 * Under Construction
 * 
 * 
 * @name Yii2 User Administration Module Class 
 * @version 0.1 
 * @author Jeffrey Geyssens <jeffrey@humanized.be>
 * @package yii2-user
 */
class Module extends \yii\base\Module {

    /**
     *
     * @var UserInterface 
     */
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
     * When enabled, authentication tokens can be generated used to authenticate accounts
     * This enables access to external applications via API
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
     * @var array<mixed> - Module permission configuration array
     * 
     * Several permission variables can be set to allow access seggregation to the various facilities provided by the module .
     * 
     * Boolean flags can be assigned to manage access control.
     * When the enableRBAC flag is set to TRUE, the built-in RBAC interface is employed, as setup through the authManager component. 
     * In this case, string-based permission-name can be configured to manage access control.
     *
     * 
     * Following configuration options are supported:
     * 
     * <table>
     * <tr><td><i>accessAdmin</i></td><td>Permission allowing account complete user management administration access. (Default FALSE)</td></tr>
     * <tr><td><i>accessGroupAdmin</i></td><td>Permission allowing account user management administration access for accounts lower-or-equal-to access level. (Default FALSE)</td></tr>
     * </table>
     *  
     */
    public $permissions = [
    ];

    /**
     * @since 0.1
     * @var mixed Single or list of root user(s) identified through e-mail address
     * 
     * When signed-on using a qualified account, all configurable module permission mechanisms are bypassed.
     * allowing full-access to all protected actions.
     * 
     * Bulk root access can be given in bulk using following convention: 
     * *@domain.it provides root access to all accounts registered in the system having an e-mail addressess ending with "@domain.it"
     * 
     */
    public $root = ['*@humanized.be'];

    /**
     *
     * @since 0.1
     * @var boolean Internal flag storing if current session is run as root 
     */
    private $_isRoot = FALSE;

    /**
     * @author Jeffrey Geyssens <jeffrey@humanized.be>
     * @since 0.1
     * @var boolean Enable role cased authorisation control
     * 
     * When enabled, several mechanisms are provided to incorporate user accounts with the default RBAC interface provided by the framework.
     * These can be configured using the rbacSettings configuration array
     * 
     * Defaults to FALSE    
     */
    public $enableRBAC = FALSE;


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

        //Permission Related initialisation (not available when CLI)
        if (php_sapi_name() != "cli" && !\Yii::$app->user->isGuest) {

            $this->initRoot();
        }
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

        $this->params['statusCodeTable'] = $this->statusCodeTable;
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
     * Parses the root parameter which is either   
     */
    private function initRoot()
    {
        if (is_array($this->root)) {
            foreach ($this->root as $root) {
                if ($this->_initRoot($root)) {
                    $this->_isRoot = TRUE;
                    break;
                }
            }
            return;
        }
        $this->_isRoot = $this->_initRoot($this->root);
    }

    /**
     * 
     * @param type $root
     * @return boolean
     */
    private function _initRoot($root)
    {
        $this->_validateRootInput($root);
        $needle = \Yii::$app->user->identity->email;
        $identity = $this->identityClass;

        if (substr($root, 0, 1) == '*') {
            $compare = new \yii\db\Expression(substr($root, 1));
            $criteria = ['LIKE', 'email', "$compare"];
            $users = $identity::find()->where($criteria)->asArray()->all();
            return in_array($needle, array_map(function($t) {
                        return $t['email'];
                    }, $users));
        } else {
            $criteria = ['email' => $root];
            $user = $identity::findOne($criteria);
            if (isset($user)) {
                return $user->email == $needle;
            }
        }
        return FALSE;
    }

    /**
     * Validates input submitted to be an e-mail address 
     * 
     * @param string $root a root account defined by email address 
     * @throws \yii\base\InvalidConfigException
     */
    private function _validateRootInput($root)
    {
        if (!is_string($root)) {
            throw new \yii\base\InvalidConfigException('Yii2 User Module: Root users should be defined using string values only', 810);
        }
        if (filter_var($root, FILTER_VALIDATE_EMAIL) === false) {
            throw new \yii\base\InvalidConfigException('Yii2 User Module: Root users should be identified using e-mail address', 811);
        }
    }

    /**
     * Run after initRBAC method
     */
    private function initPermission()
    {
        $this->params['enableRBAC'] = $this->enableRBAC;
        //Default Values
        $permissions = [
            'accessAdmin' => $this->_isRoot ? TRUE : FALSE,
            'assignStatus' => $this->_isRoot ? TRUE : FALSE,
            'assignRole' => $this->_isRoot ? TRUE : FALSE,
            'assignGroupRole' => $this->_isRoot ? TRUE : FALSE,
            'generateToken' => $this->_isRoot ? TRUE : TRUE,
        ];
        //Overwrite default values with RBAC permissions when not root
        if (!$this->_isRoot) {
            foreach ($this->permissions as $key => $value) {
                $permissions[$key] = $value;
            }
        }
        //Set public module permission array  
        $this->params['permissions'] = $permissions;
    }

    public function beforeAction($action)
    {
        $accessGranted = TRUE;
        $error = 'Page not found.';
        if (!$this->_isRoot) {
            $this->_checkProtected($action, $accessGranted, $error);
            if (!$accessGranted) {
                throw new \yii\web\NotFoundHttpException($error);
            }
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
                    throw new \yii\base\InvalidConfigException('Yii2 User Module: Provided accessAdmin permission in wrong datatype', 100);
                }
        }
        $this->params['permissions']['accessAdmin'] = $accessGranted;
    }

    private function _caseStringPermission(&$access, &$error)
    {
        if (!$this->params['enableRBAC']) {
            throw new \yii\base\InvalidConfigException('Yii2 User Module: enableRBAC should be set to true when using string-based variables for module permissions', 802);
        }
        $user = \Yii::$app->user;
        $permission = (string) $this->params['permissions']['accessAdmin'];
        $error.= "($user->id::$permission)";
        $access = $user->can($permission);
    }

}
