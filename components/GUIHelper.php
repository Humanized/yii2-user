<?php

namespace humanized\user\components;

use yii\helpers\ArrayHelper;

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

    public static function getStatusList()
    {
        $module = \Yii::$app->controller->module;
        if ($module->params['enableStatusCodes']) {
            
        }
    }

    public static function getStatusListFromDB()
    {
        
    }

}
