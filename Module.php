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

    public $enableRBAC = false;

    public function init()
    {
        parent::init();
        if (\Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'humanized\user\commands';
        }
    }

}
