<?php

namespace humanized\user\components;

use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/**
 * User Account Grid Widget for Yii2 - By Humanized
 * 
 * Provides an all-purpose user management dashboard
 *  
 *
 * @name Yii2  Account Grid Widget Class
 * @version 0.1 
 * @author Jeffrey Geyssens <jeffrey@humanized.be>
 * @package yii2-user
 */
class AccountGrid extends Widget {

    /**
     *
     * @var yii\db\ActiveRecord
     */
    public $searchModel = NULL;

    /**
     *
     * @var yii\db\ActiveRecord
     */
    public $dataProvider = NULL;
    public $actionTemplate = '{view} {verify} {delete}';
    public $deleteButtonCallback = NULL;
    public $verifyButtonCallback = NULL;
    public $viewButtonCallback = NULL;

    /**
     * @since 0.1
     * @var boolean 
     * 
     * 
     * Defaults to TRUE
     * 
     */
    public $enable = TRUE;
    public $canDeleteAccount = TRUE;
    public $canVerifyAccount = TRUE;
    public $canViewAccount = TRUE;
    public $displayStatusColumn = TRUE;
    public $displayCreatedAt = FALSE;
    public $displayUpdatedAt = FALSE;

    /**
     * @since 0.1
     * @var boolean Enable widget visualisation
     * 
     * 
     * Defaults to TRUE
     * 
     */
    public $enableRBAC = TRUE;
    private $_columns = [];

    /**
     * 
     */
    public function init()
    {

        parent::init();

        //Throw an Exception if model is not provided
        if (!isset($this->searchModel) || !isset($this->dataProvider)) {
            return;
        }

        if ($this->canViewAccount && !isset($this->viewButtonCallback)) {
            $this->_setupViewButtonCallback();
        }
        if ($this->canVerifyAccount && !isset($this->verifyButtonCallback)) {
            $this->_setupVerifyButtonCallback();
        }
        if ($this->canDeleteAccount && !isset($this->deleteButtonCallback)) {
            $this->_setupDeleteButtonCallback();
        }

        $this->_initColumns();
    }

    /**
     * 
     */
    public function run()
    {
        $config = [
            'dataProvider' => $this->dataProvider,
            'columns' => $this->_columns,
        ];

        return \yii\grid\GridView::widget($config);
    }

    private function _initColumns()
    {
        $this->_setupActionColumns();
        $this->_setupStatusColumns();
        $this->_setupIdentificationColumns();
        $this->_setupTimestampColumns();
    }

    private function _setupActionColumns()
    {
        $this->_columns[] = [
            'class' => \yii\grid\ActionColumn::className(),
            'template' => $this->actionTemplate,
            'buttons' => [
                'view' => $this->viewButtonCallback,
                'verify' => $this->verifyButtonCallback,
                'delete' => $this->deleteButtonCallback]
        ];
    }

    private function _setupIdentificationColumns()
    {
        /* Check if username column exists in corresponding AR database table */
        if ($this->searchModel->hasAttribute('username')) {
            echo $this->form->field($this->model, 'username')->input('username');
        }
        $this->_columns[] = 'email:email';
    }

    private function _setupTimestampColumns()
    {
        if ($this->displayCreatedAt) {
            $this->_columns[] = 'created_at:datetime';
        }
        if ($this->displayUpdatedAt) {
            $this->_columns[] = 'updated_at:datetime';
        }
    }

    private function _setupStatusColumns()
    {
        if ($this->searchModel->hasAttribute('status') && $this->displayStatusColumn) {
            $this->_columns[] = ['label' => 'Status', 'format' => 'html', 'value' => function ($model, $key, $index, $column) {
                    $inactive = ((int) $model['status'] == 0 ? TRUE : FALSE);
                    return \humanized\user\components\GUIHelper::getStatusOutput($inactive);
                }];
        }
    }

    private function _setupViewButtonCallback()
    {
        $this->viewButtonCallback = function ($url, $model, $key) {
            $options = [
                'title' => \Yii::t('yii', 'Account Details'),
                'aria-label' => \Yii::t('yii', 'Account Details'),
                'data-pjax' => '0'];

            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/user/account/index', 'id' => $model['id']], $options);
        };
    }

    private function _setupVerifyButtonCallback()
    {
        $this->verifyButtonCallback = function ($url, $model, $key) {

            $options = [
                'title' => \Yii::t('yii', ((int) $model['status'] == 0 ? 'Enable Account' : 'Disable Account')),
                'aria-label' => \Yii::t('yii', ((int) $model['status'] == 0 ? 'Enable Account' : 'Disable Account')),
                'data-pjax' => '0',
                'visible' => $model['id'] == \Yii::$app->user->id,
                'hidden' => $model['id'] == \Yii::$app->user->id,
            ];

            return Html::a('<span class="glyphicon glyphicon-' . ((int) $model['status'] == 0 ? 'play' : 'stop') . '"></span>', ['/user/admin/verify', 'id' => $model['id']], $options);
        };
    }

    private function _setupDeleteButtonCallback()
    {
        $this->deleteButtonCallback = function ($url, $model, $key) {

            $options = [
                'visible' => (int) $model['status'] != 0 ? TRUE : FALSE,
                'hidden' => (int) $model['status'] != 0 ? TRUE : FALSE,
                'title' => \Yii::t('yii', 'Delete Account'),
                'aria-label' => \Yii::t('yii', 'Delete Account'),
                'data-confirm' => \Yii::t('yii', 'Are you sure you want to delete this account?'),
                'data-method' => 'post',
                'data-pjax' => '0',
            ];
            return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model['id']], $options);
        };
    }

}
