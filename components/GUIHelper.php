<?php

namespace humanized\user\components;

/**
 * A collection of static helper functions to implement the user management 
 */
class GUIHelper {

    public static function getMenuItems()
    {
        $output = [];
        $output[] = ['label' => 'User Management', 'url' => ['/user/admin/index']];
        if (NULL !== \Yii::$app->user->getId()) {
            $output[] = ['label' => 'Account', 'url' => ['/user/admin/account', 'id' => \Yii::$app->user->getId()]];
            // $output[] = ['label' => '', 'url' => ['/user/admin/account', 'id' => \Yii::$app->user->getId()]];
        }
        return $output;
    }

    /**
     * Returns the list 
     * 
     * @return array<string>|null List of User Account Status Options or NULL when module is set to ignore this feature
     */
    public static function getStatusList()
    {
        return \Yii::$app->controller->module->params['statusCodes'];
    }

}
