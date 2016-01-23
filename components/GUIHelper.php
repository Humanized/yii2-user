<?php

namespace humanized\user\components;

use yii\helpers\Html;

/**
 * A collection of static helper functions to implement the user management 
 */
class GUIHelper {

    public static function getUserMenuItems()
    {

        $menuItems = [];
        if (\Yii::$app->user->isGuest) {
            //$menuItems[] = ['label' => 'Signup', 'visible' => \humanized\user\Module::getInstance()->params['enableSignUp'], 'url' => ['/user/account/signup']];
            $menuItems[] = ['label' => 'Login', 'url' => ['/user/account/login']];
        } else {
            $menuItems[] = '<li>'
                    . Html::beginForm(['/user/account/logout'], 'post')
                    . Html::submitButton(
                            'Logout (' . \Yii::$app->user->identity->email . ')', ['class' => 'btn btn-link']
                    )
                    . Html::endForm()
                    . '</li>';
        }
        return $menuItems;
    }

    public static function getMenuItems()
    {
        $output = [];
        $output[] = ['label' => 'User Management', 'visible' => \Yii::$app->controller->module->params['permissions']['accessAdmin'], 'url' => ['/user/admin/index']];
        if (NULL !== \Yii::$app->user->id) {
            $output[] = ['label' => 'My Profile', 'url' => ['/user/account/index', 'id' => \Yii::$app->user->getId()]];
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
        return \humanized\user\Module::getInstance()->params['statusCodes'];
    }

}
