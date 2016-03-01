<?php

namespace humanized\user\components;

use yii\base\Widget;
use yii\helpers\Html;

/**
 * Authentication Widget for Yii2 - By Humanized
 * 
 * This widget allows the user to set the application using the configuration provided by the Module. 
 * 
 * @name Yii2 LanguagePicker Widget Class
 * @version 0.1 
 * @author Jeffrey Geyssens <jeffrey@humanized.be>
 * @package yii2-translation
 */
class Authentication extends Widget
{

    public $append = FALSE;
    public $enableSignUp = FALSE;
    public $guestTemplate = '{signup}{login}';
    public $template = '{logout}';
    public $separator = ' | ';

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $out = '';
        if (\Yii::$app->user->isGuest) {

            if ($this->enableSignUp) {
                $out .= \yii\bootstrap\Html::a('Signup', ['/user/account/signup']);
                $out .= $this->separator;
            }
            $out .= \yii\bootstrap\Html::a('Login', ['/user/account/login']);
        } else {
            $out = '<li>'
                    . Html::beginForm(['/user/account/logout'], 'post')
                    . Html::submitButton(
                            'Logout (' . \Yii::$app->user->identity->email . ')', ['class' => 'btn btn-link']
                    )
                    . Html::endForm()
                    . '</li>';
        }
        return $out;
    }

    private function _getList()
    {
        $menuItems = [];
        if (\Yii::$app->user->isGuest) {
            //          $menuItems[] = ['label' => 'Signup', 'visible' => \humanized\user\Module::getInstance()->params['enableSignUp'], 'url' => ['/user/account/signup']];
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

        return $urls;
    }

}
