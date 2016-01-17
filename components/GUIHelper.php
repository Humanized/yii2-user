<?php

namespace humanized\user\components;

use yii\helpers\ArrayHelper;

class GUIHelper {

    private $_module = NULL;

    public function __construct()
    {
        $this->$_module = \Yii::$app->controller->module;
    }

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
        if ($this->_module->params['enableStatusCodes']) {
            return $this->_module->statusCodes;
        }
        return NULL;
    }

}
