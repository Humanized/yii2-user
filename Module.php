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
 * @name Yii2 User Administration Module Class 
 * @version 0.1 
 * @author Jeffrey Geyssens <jeffrey@humanized.be>
 * @package yii2-user
 */
class Module extends \yii\base\Module
{

    /**
     *
     * @var UserInterface - Considers IdentityClass set in user component is default
     */
    public $identityClass = NULL;

    /**
     *
     * @var callable - Function taking some string returning the user model. By default findUser defined by the user component is used as default
     */
    public $fnUser = NULL;

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
     * 
     * <tr><td><i>access.dashboard</i></td><td>Permission allowing access to the user management dashboard. Default FALSE</td></tr>
     * <tr><td><i>create.account</i></td><td>Permission allowing creation of user accounts. Allows assigning users with role equal than or lower than administrator role undertaking the action. Default FALSE</td></tr>
     * <tr><td><i>delete.account</i></td><td>Permission allowing deletion of user accounts. Default FALSE</td></tr>
     * <tr><td><i>verify.account</i></td><td>Permission allowing verification of user accounts. Useful when the enableAdminVerification flag is set to TRUE. Default FALSE</td></tr>
     * </table>
     *  
     */
    public $permissions = [
    ];
    public $gridOptions = [
    ];
    public $formOptions = [
    ];
    public $detailOptions = [
    ];

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
     * @var boolean Enable user verification of public account creation through email
     * 
     * When enabled, account creation must be confirmed through the user management facilities provided by the module
     * 
     * Defaults to FALSE
     * 
     */
    public $enableAdminVerification = FALSE;

    /**
     * @todo Implement this feature
     * @var boolean Enable administrator verification of public account creation
     * 
     * When enabled, administrators require to confirm user registration through the administrator dashboard.
     *  
     * 
     * Defaults to FALSE
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
    public $displayTimestamps = TRUE;
    public $displayCreatedAt = TRUE;
    public $displayUpdatedAt = TRUE;
    public $statusCodeTable = NULL;
    public $statusCodeTableId = 'id';
    public $statusCodeTableName = 'name';
    public $statusCodes = [];
    public $defaultStatusCode = 10;

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
    public $root = ['*@domain.it'];

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

    /**
     * @author Jeffrey Geyssens <jeffrey@humanized.be>
     * @since 0.1
     * @var boolean
     * 
     * When enabled, RBAC roles are colored using additional configuration options.
     * 
     * By default, once set to true, this module depends on the existence of http://www.github.com/humanized/yii2-rbac) to provide the mapping mechanism between RBAC roles and color codes
     * 
     * The dependency can be resolved true manual override however, by setting the roleColor option.
     * 
     * By default,  
     * 
     * 
     * 
     * 
     */
    public $colorRoles = FALSE;

    /**
     * @author Jeffrey Geyssens <jeffrey@humanized.be>
     * @since 0.1
     * @var NULL|Array<string:string>|String
     * 
     * Disabled when set to NULL
     * 
     * Configure to enable using following datatypes:
     * 
     * <ul>
     * <li>An array: Mapping role-names (array-key) to color codes </li>   
     * <li>A string: Providing a path to some class that implements a static function getColor method taking a role-name as parameter. </li>
     * </ul>
     * 
     * Color codes are strings interpreted as followed:
     * <ul>
     * <li>Prefixed using either # or (at): color is interpreted as HTML color code (prefix (at) is ignored to allow specification of HTML color names) </li>
     * <li>Else, it is considered a classname</li>
     * </ul>
     */
    public $roleColors = NULL;

    /**
     * @author Jeffrey Geyssens <jeffrey@humanized.be>
     * @since 0.1
     * @var array<string> List of actions when guest
     */
    private $_public = ['login', 'request-password-reset', 'reset-password'];

    /**
     * Initialisation of module parameters
     * Is run when loading configuration file
     * 
     * @author Jeffrey Geyssens <jeffrey@humanized.be>
     * @since 0.1
     */
    public function init()
    {
        parent::init();
        if (\Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'humanized\user\commands';
        }


        /**
         * Configure Global Module Options
         */
        $this->initIdentityModel();
        $this->initModuleOptions();
        $this->initStatusCodes();
        $this->initTimestamps();

        /**
         * Configure Module Interface Options
         */
        $this->params['gridOptions'] = $this->gridOptions;
        $this->params['formOptions'] = $this->formOptions;
        $this->params['detailOptions'] = $this->detailOptions;
        //Permission Related initialisation (not available when CLI)
        //     var_dump(\Yii::$app->user);
        /*
          if (php_sapi_name() != "cli" && !\Yii::$app->user->isGuest) {
          $this->initRoot();
          }
         * 
         */


        $this->initRBAC();
        $this->initPermission();
    }

    /**
     * Initialisation of internal reference to identityclass
     * 
     * @author Jeffrey Geyssens <jeffrey@humanized.be>
     * @since 0.1 
     */
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

    /**
     * Initialisation of global module options
     *  
     * @author Jeffrey Geyssens <jeffrey@humanized.be>
     * @since 0.1
     */
    private function initModuleOptions()
    {
        $this->params['enableTokenAuthentication'] = $this->enableTokenAuthentication;
        $this->params['enableUserName'] = $this->enableUserName;
        $this->params['enableAdminVerification'] = $this->enableAdminVerification;
        $this->params['enableUserVerification'] = $this->enableUserVerification;
        $this->params['enablePasswords'] = $this->enablePasswords;
        $this->params['enableSignUp'] = $this->enableSignUp;
        if ($this->enableSignUp) {
            $this->_public[] = 'signup';
        }
    }

    /**
     * Initialisation of grid options
     * 
     * @todo Create Dashboard widget and allow setup options override
     * @author Jeffrey Geyssens <jeffrey@humanized.be>
     * @since 0.1
     */
    private function initGridOptions()
    {

        /*
          $this->params['enablePjax'] = $this->enablePjax;
          $this->params['enableKartik'] = $this->enablePjax;
          $this->params['enableDynagrid'] = $this->enableRBAC;
         * 
         */
    }

    /**
     * Initialisation of status code mechanism
     * 
     * @todo Database Status Code Storage
     * @author Jeffrey Geyssens <jeffrey@humanized.be>
     * @since 0.1
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

    private function initTimestamps()
    {
        $this->params['displayTimestamps'] = $this->displayTimestamps;
        $this->params['displayCreatedAt'] = $this->displayCreatedAt;
        $this->params['displayUpdatedAt'] = $this->displayUpdatedAt;
    }

    /**
     * <p>Sets the internal is_root flag for a session, allowing overriding of module RBAC permission checks.</p>  
     * <p>Relevant parameter is either single value or array
     * Subsequently, a private function is called either on a single string value once or, in succession on multiple string values.</p>  
     * @author Jeffrey Geyssens <jeffrey@humanized.be>
     * @since 0.1 
     */
    private function initRoot()
    {
        //CASE#1: setup parameter is a single value 
        if (!is_array($this->root)) {
            //Call private method returning a boolean type
            $this->_isRoot = $this->_initRoot($this->root);
            return;
        }
        //CASE#2: setup parameter is an array
        foreach ($this->root as $root) {
            //Call private method returning a boolean type
            if ($this->_initRoot($root)) {
                //Break at first evaluation to true
                $this->_isRoot = TRUE;
                break;
            }
        }
        //When Foreach passes without break, flag remains set to default
        return;
    }

    /**
     * Validates e-mail input is submitted for assigning root access   
     * Throws appropriate error on config validation error
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
     * Private boolean function that determines session root access 
     * given user credentials and module configuration 
     * @param type $root
     * @return boolean
     */
    private function _initRoot($root)
    {
        $this->_validateRootInput($root);
        $needle = \Yii::$app->user->identity->email;
        $identity = $this->identityClass;
        //CASE #1: Bulk root assignment based on email suffix
        if (substr($root, 0, 1) == '*') {
            $compare = new \yii\db\Expression(substr($root, 1));
            $criteria = ['LIKE', 'email', "$compare"];
            $users = $identity::find()->where($criteria)->asArray()->all();
            return in_array($needle, array_map(function($t) {
                        return $t['email'];
                    }, $users));
        }
        //CASE #2: Single account entry
        $criteria = ['email' => $root];
        $user = $identity::findOne($criteria);
        if (isset($user)) {
            return $user->email == $needle;
        }
        //ELSE: Return FALSE (no root access)
        return FALSE;
    }

    private function initRBAC()
    {
        //Role coloring mechanism
        if ($this->colorRoles) {
            //Setup alternative role display callback 

            if (!isset($this->roleColors)) {
                //Use Humanized RBAC as default
            }

            $this->params['colorRoles'] = $this->colorRoles;
            $this->params['roleColors'] = $this->roleColors;
            $colors = $this->params['roleColors'];

            if (!isset($this->detailOptions['roleMapCallback'])) {
                $callback = function($role) use($colors) {
                    $class = 'label';
                    $style = '';
                    $color = $colors[$role->name];
                    if (substr($color, 0, 1) != "#" && substr($color, 0, 1) != "@") {
                        $class .= ' ' . $color;
                    } else {
                        $color = str_replace("@", "", $color);

                        $style = ' ' . 'style="background-color:' . $color . ';"';
                    }

                    $out = '<div class="' . $class . '"' . $style . '>';
                    $out .= $role->name;
                    $out .= '</div>';
                    return $out;
                };
                $this->params['detailOptions']['roleMapCallback'] = $callback;
            }
        }
    }

    /**
     * Run after initRBAC method
     */
    private function initPermission()
    {
        $this->params['enableRBAC'] = $this->enableRBAC;
        //Setup defaults
        $permissions = [
            'access.dashboard' => $this->_isRoot ? TRUE : FALSE,
            'create.account' => $this->_isRoot ? TRUE : FALSE,
            'delete.account' => $this->_isRoot ? TRUE : FALSE,
            'view.account' => $this->_isRoot ? TRUE : FALSE,
            'verify.account' => $this->_isRoot ? TRUE : FALSE,
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

    /**
     * Global Module beforeActions
     * Defines controller access permission based on module configuration
     * 
     * 
     * @param type $action
     * @return type
     * @throws \yii\web\NotFoundHttpException
     */
    public function beforeAction($action)
    {
        //Access Granted By Default
        //Default Error Message
        $error = 'Page not found.';

        //CASE #1: Public Access (Guest Access)
        if (\Yii::$app->user->isGuest) {
            if (in_array($action->id, $this->_public)) {
                return parent::beforeAction($action);
            } else {
                throw new \yii\web\NotFoundHttpException($error);
            }
        }
        //CASE #2: Configurable Interfaces
        if (($action->id == 'tokens' || ($action->id == 'delete-token')) && !$this->params['enableTokenAuthentication']) {
            throw new \yii\web\NotFoundHttpException($error);
        }

        //CASE #3: Permission-based Access
        if (!$this->_isRoot) {
            //Not Root Access, so see if particular 
            $accessGranted = $this->_checkPrivilege($action);
            if (!$accessGranted) {
                throw new \yii\web\NotFoundHttpException($error);
            }
        }
        return parent::beforeAction($action);
    }

    /**
     * 
     * Is called when session has a logged-in users
     * Bail out of controller when this function returns NULL
     * @param type $action
     * @throws \yii\base\InvalidConfigException
     */
    private function _checkPrivilege($action)
    {
        $access = NULL;
        switch (\Yii::$app->controller->id) {
            case 'admin': {
                    $access = $this->_checkAdminPrivilege($action);
                    break;
                }
            case 'account': {
                    $access = $this->_checkAccountPrivilege($action);
                    break;
                }
        }
        return $access;
    }

    private function _checkAccountPrivilege($action)
    {
        $access = FALSE;
        switch ($action->id) {
            case 'logout': {
                    $access = TRUE;
                    break;
                }
            case 'index' || 'tokens' || 'delete-token' || 'request-password-reset': {
                    $access = $this->_validateAccountParameters($action->id);
                    if ($action->id == 'index') {
                        $access = $access || $this->_switchPermission('view.account');
                    }
                    break;
                }
            case 'view': {

                    return;
                }
        }
        return $access;
    }

    private function _checkAdminPrivilege($action)
    {
        $access = FALSE;
        switch ($action->id) {
            case 'index': {
                    $access = $this->_switchPermission('access.dashboard');
                    break;
                }
            case 'delete': {
                    $access = $this->_switchPermission('delete.account');
                    break;
                }
            //TODO: implement this action to segregate 
            case ('verify'): {
                    $access = $this->_switchPermission('verify.account');
                    break;
                }
        }
        return $access;
    }

    private function _validateAccountParameters($action)
    {
        if ($action == 'reset-password') {
            return true;
        }
        $id = \yii::$app->getRequest()->getQueryParams()['id'];
        $userId = \Yii::$app->user->id;
        if (!isset($id)) {
            return FALSE;
        }


            //User ID parameter is set and matches current session account-id
        //Only users can delete their own tokens (for now)
        if ($action != 'delete-token') {
            return $userId == $id;
        }
        //delete token
        $token = models\common\AuthenticationToken::findOne(['id' => $id]);
        if (isset($token)) {
            return $token->user_id == $userId;
        }
        return FALSE;
    }

    private function _switchPermission($permission)
    {
        $permissionGranted = FALSE;
        $permissionParam = $this->params['permissions'][$permission];
        //         var_dump($this->params['permissions'][$permission]);


        switch (gettype($permissionParam)) {
            case "boolean": {
                    $permissionGranted = $permissionParam;
                    break;
                }
            case "string": {
                    if (!$this->params['enableRBAC']) {
                        throw new \yii\base\InvalidConfigException('Yii2 User Module: enableRBAC should be set to true when using string-based variables for module permissions', 802);
                    }
                    $permissionGranted = \Yii::$app->user->can($permissionParam);
                    break;
                }
            default: {
                    throw new \yii\base\InvalidConfigException('Yii2 User Module: Permission provided in wrong datatype', 100);
                }
        }

        return $permissionGranted;
    }

}
