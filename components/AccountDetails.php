<?php

namespace humanized\user\components;

use yii\base\Widget;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;
use yii\bootstrap\Html;

/**
 * User Account Details Widget for Yii2 - By Humanized
 * 
 * Displays individual user account data 
 *  
 *
 * @name Yii2  Account DetailView Widget Class
 * @version 0.1 
 * @author Jeffrey Geyssens <jeffrey@humanized.be>
 * @package yii2-user
 */
class AccountDetails extends Widget {

    const DISPLAY_ROLE_DEFAULT = 0;
    const DISPLAY_ROLE_ALL = 1;
    const DISPLAY_ROLE_COMBINED = 2;

    /**
     *
     * @var yii\db\ActiveRecord
     */
    public $model = NULL;
    public $displayStatusFields = TRUE;
    public $displayCreatedAt = TRUE;
    public $displayUpdatedAt = TRUE;
    public $canDeleteAccount = FALSE;
    public $canVerifyAccount = TRUE;
    public $enableRBAC = FALSE;
    public $rbacAttributes = [];
    public $displayRBACFields = FALSE;
    public $displayRBACMode = self::DISPLAY_ROLE_DEFAULT;
    public $roleMapCallback = NULL;
    public $roleMapImplodeSeperator = ' ';
    private $_attributes = [];

    /**
     * 
     */
    public function init()
    {

        parent::init();

        if ($this->enableRBAC && $this->displayRBACFields && empty($this->rbacAttributes)) {

            if (!isset($this->displayRBACMode)) {
                $this->displayRBACMode = self::DISPLAY_ROLE_DEFAULT;
            }

            if (!isset($this->roleMapCallback)) {
                $this->roleMapCallback = function($r) {
                    return $r->name;
                };
            }
            $this->_initRbacAttributes();
        }
    }

    private function _initRbacAttributes()
    {
        $direct = \Yii::$app->authManager->getRolesByUser($this->model->id);
        $out = [];
        $value = NULL;

        switch ($this->displayRBACMode) {
            case self::DISPLAY_ROLE_DEFAULT: {
                    $this->rbacAttributes[] = ['label' => 'Roles', 'format' => 'html', 'value' => implode($this->roleMapImplodeSeperator, array_map($this->roleMapCallback, $direct))];
                    break;
                }
            case self::DISPLAY_ROLE_ALL: {
                    $this->rbacAttributes[] = ['label' => 'Direct Roles', 'format' => 'html', 'value' => implode($this->roleMapImplodeSeperator, array_map($this->roleMapCallback, $direct))];
                    $callback = function($r) {
                        return array_map($this->roleMapCallback, \Yii::$app->authManager->getChildren($r->name));
                    };
                    $this->rbacAttributes[] = ['label' => 'Indirect Roles', 'format' => 'html', 'value' => implode($this->roleMapImplodeSeperator, array_merge(array_map($callback, $direct)))];

                    break;
                }
            case self::DISPLAY_ROLE_COMBINED: {


                    break;
                }
        }
    }

    /**
     * 
     */
    public function run()
    {
        $this->_setupAttributes();
        $out = DetailView::widget([
                    'model' => $this->model,
                    'attributes' => $this->_attributes,
        ]);

        if ($this->model->id != \Yii::$app->user->id && \Yii::$app->controller->module->params['enableStatusCodes'] && Yii::$app->controller->module->params['permissions']['verify.account']) {

            if ($this->canVerifyAccount) {
                $out .= Html::a(($this->model->status == 0 ? 'Enable' : 'Disable') . ' Account', ['admin/verify', 'id' => $model->id, 'alt' => TRUE], ['class' => 'btn btn-' . ($model->status == 0 ? 'success' : 'danger')]);
                $out .= ' ';
            }
        }

        $out .= Html::a('Reset Password', ['request-password-reset', 'id' => $this->model->id], ['class' => 'btn btn-success']);
        return $out;
    }

    private function _setupAttributes()
    {
        $this->_setupStatusAttributes();
        $this->_setupIdentificationAttributes();
        $this->_setupRbacAttributes();
        $this->_setupTimestampAttributes();
    }

    private function _setupIdentificationAttributes()
    {
        /* Check if username column exists in corresponding AR database table */
        if ($this->model->hasAttribute('username')) {
            echo $this->form->field($this->model, 'username')->input('username');
        }
        $this->_attributes[] = 'email:email';
    }

    private function _setupTimestampAttributes()
    {
        if ($this->displayCreatedAt) {
            $this->_attributes[] = 'created_at:datetime';
        }
        if ($this->displayUpdatedAt) {
            $this->_attributes[] = 'updated_at:datetime';
        }
    }

    private function _setupStatusAttributes()
    {
        if ($this->displayStatusFields && $this->model->hasAttribute('status')) {
            $this->_attributes[] = ['label' => 'Status', 'format' => 'html', 'value' => \humanized\user\components\GUIHelper::getStatusOutput(((int) $this->model->status == 0))];
        }
    }

    private function _setupRbacAttributes()
    {
        if ($this->enableRBAC && $this->displayRBACFields) {
            $this->_attributes[] = array_merge($this->_attributes, $this->rbacAttributes);
        }
    }

}