<?php

namespace humanized\user;

/**
 * User Administration Module for Yii2 - By Humanized
 * This module wraps and extends the default user management interface provided by the framework. 
 *
 * It is designed to be fully compatible with the factory-provided implementations related to user management.
 * For this, several configuration options can be set to override module defaults allowing the use of the factory default or custom classes.
 * 
 * 
 * 
 * 
 * Provides several interfaces for dealing with Yii2 based user accounts:
 * 
 * GUI
 * 
 * This module wraps the default user-management provided by the system
 * 
 * 
 * REST API
 * 
 * Under Construction - Due v0.5
 * 
 * CLI 
 * 
 * A CLI allowing basic Yii2 user administration functions.
 * 
 * 
 * @name Yii2 User Administration Module CLass 
 * @version 0.0.1 
 * @author Jeffrey Geyssens <jeffrey@humanized.be>
 * @package yii2-user
 */
class Module extends \yii\base\Module {

    public $identityClass = NULL;
    public $fnUser = NULL;
    public $emailOnly = false;
    public $enableSignUp = true;
    public $enablePasswords = true;
    public $enablePjax = false;
    public $enableRBAC = false;
    public $enableKartik = false;
    public $enableDynaGrid = false;
    //User Status Code
    public $enableStatusCodes = true;
    public $statusCodeTable = NULL;
    public $statusCodeTableId = 'id';
    public $statusCodeTableName = 'name';
    public $statusCodes = [];
    public $defaultStatusCode = 0;

    public function init()
    {
        parent::init();
        if (\Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'humanized\user\commands';
        }
        $this->initIdentityModel();
        $this->initGridOptions();

        if ($this->enableStatusCodes) {
            $this->initStatusCodes();
        }
        $this->params['enableRBAC'] = $this->enableRBAC;
        $this->params['emailOnly'] = $this->emailOnly;
        $this->params['enablePasswords'] = $this->enablePasswords;
        $this->params['enableSignUp'] = $this->enablePasswords;
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
    }

}
