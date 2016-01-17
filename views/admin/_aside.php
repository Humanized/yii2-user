<?php

/**
 * Exemplatory aside menu
 * 
 * While this view file is subjective, the usage of GUIHelper is emphasised 
 * particularly in function of menu setup common to framework practises
 * 
 */
use yii\widgets\Menu;
use humanized\user\components\GUIHelper;

echo Menu::widget([
    'items' => GUIHelper::getMenuItems(),
    'options' => [
        'class' => 'nav nav-pills nav-stacked',
        'id' => 'navbar-id',
        'style' => 'font-size: 14px;',
        'data-tag' => 'yii2-menu',
    ],
]);
