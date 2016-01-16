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
    public $enablePjax = false;
    public $enableRBAC = false;
    public $enableKartik = false;
    public $enableDynaGrid = false;

    public function init()
    {
        parent::init();


        if (\Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'humanized\user\commands';
        }

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
        $this->params['enablePjax'] = $this->enablePjax;
        $this->params['enableRBAC'] = $this->enableRBAC;
        $this->params['enableKartik'] = $this->enablePjax;
        $this->params['enableDynagrid'] = $this->enableRBAC;
    }

}
