<?php

namespace humanized\user\components;

use yii\helpers\Html;

/**
 * A collection of static helper functions to implement the user management 
 */
class GUIHelper {

    public static function getMenuItems()
    {
        $output = [];
        if (NULL !== \Yii::$app->user->id) {
            $output[] = ['label' => 'Account', 'url' => ['/user/account/index', 'id' => \Yii::$app->user->getId()]];
        }
        if (NULL !== \Yii::$app->user->id) {
            $output[] = ['label' => 'Authentication Tokens', 'visible' => \Yii::$app->controller->module->params['enableTokenAuthentication'], 'url' => ['/user/account/tokens', 'id' => \Yii::$app->user->getId()]];
        }

        $output[] = ['label' => 'User Management', 'visible' => \Yii::$app->controller->module->params['permissions']['access.dashboard'], 'url' => ['/user/admin/index']];
        return $output;
    }

    /**
     * Returns the list 
     * 
     * @return array<string>|null List of User Account Status Options or NULL when module is set to ignore this feature
     */
    public static function getStatusList()
    {
        return \humanized\user\Module::getInstance()->params['statusCodes'];
    }

    public static function getStatusOutput($inactive)
    {
        return'<span style="color:' . ($inactive == FALSE ? 'green' : 'red') . '" class="glyphicon glyphicon-' . ($inactive == FALSE ? 'ok' : 'remove') . '"></span>';
    }

    /**
     * Returns the list 
     * 
     * @return array<string>|null List of User Account Status Options or NULL when module is set to ignore this feature
     */
    public static function getRoleList()
    {
        return array_map(function($role) {
            return $role->name;
        }, \Yii::$app->authManager->getRoles());
    }

}
