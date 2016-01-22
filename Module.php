<?php

namespace humanized\user;

/**
 * User Administration Module for Yii2
 * 
 * This module wraps and extends the basic user management features, provided by the stock advanced Yii2 framework template.
 * 
 * Though implementations vary, the code interface as provided by the advanced template has been ported in it's entirety.
 * 
 * This allows using the module (with it's default settings)  to be used as a drop-in replacement for default user managment,
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
     * @var boolean Enable role cased authorisation control
     * 
     * When enabled, several mechanisms are provided to incorporate user accounts with the default RBAC interface provided by the framework.
     * 
     * Defaults to FALSE    
     */
    public $enableRBAC = FALSE;

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

        $this->params['enableRBAC'] = $this->enableRBAC;
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

}
