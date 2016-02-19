<?php

namespace humanized\user\components;

use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/**
 * User Account Creation Widget for Yii2 - By Humanized
 * 
 * Provides an all-purpose user creation form for use in different scenarios (i.e. administration or signup)
 * 
 * 
 * 
 *  
 *
 * @name Yii2 User Creation Form Widget Class
 * @version 0.1 
 * @author Jeffrey Geyssens <jeffrey@humanized.be>
 * @package yii2-user
 */
class AccountCreateForm extends Widget {

    /**
     *
     * @var yii\db\ActiveRecord
     */
    public $model = NULL;

    /**
     *
     * @var yii\widgets\ActiveForm
     */
    public $form = NULL;

    /**
     * @since 0.1
     * @var boolean Enable widget visualisation
     * 
     * 
     * Defaults to TRUE
     * 
     */
    public $enable = TRUE;

    /**
     * @since 0.1
     * @var boolean 
     * 
     * 
     * Defaults to TRUE
     * 
     */
    public $enableStatusDropdown = TRUE;

    /**
     *
     * @var type 
     */
    public $statusDropdownData = [0 => 'INACTIVE', 10 => 'ACTIVE'];

    /**
     * @since 0.1
     * @var boolean Enable widget visualisation
     * 
     * 
     * Defaults to TRUE
     * 
     */
    public $enablePasswordGeneration = TRUE;

    /**
     *
     * @var type 
     */
    public $forcePasswordGeneration = TRUE;

    /**
     * @since 0.1
     * @var boolean Enable widget visualisation
     * 
     * 
     * Defaults to TRUE
     * 
     */
    public $enableRBAC = TRUE;
    public $multiRole = TRUE;
    public $roleDropdownData = [];

    /**
     * @since 0.1
     * @var array<string>=function($model,$form) Callback functions to add extra form attributes before generated widget output. 
     * 
     * Following array keys are supported:
     * 
     * <table>
     * <tr><td><i>identification</i></td><td>Callback is launched before printing username/email combination fields</td></tr>
     * <tr><td><i>status</i></td><td>Callback is launched before printing status assignment field</td></tr>
     * <tr><td><i>role</i></td><td>Callback is launched before printing role assignment field</td></tr>
     * <tr><td><i>password</i></td><td>Callback is launched before printing password setup fields</td></tr>
     * </table>
     *  
     * 
     */
    public $extraAttributesBefore = [];

    /**
     * @since 0.1
     * @var array<string>=function($model,$form) Callback functions to add extra form attributes before generated widget output. 
     * 
     *  Following array keys are supported:
     * 
     * <table>
     * <tr><td><i>identification</i></td><td>Callback is launched after printing username/email combination fields</td></tr>
     * <tr><td><i>status</i></td><td>Callback is launched after printing status assignment field</td></tr>
     * <tr><td><i>role</i></td><td>Callback is launched after printing role assignment field</td></tr>
     * <tr><td><i>password</i></td><td>Callback is launched after printing password setup fields</td></tr>
     * </table>
     *  
     * 
     * Defaults to NULL
     * 
     */
    public $extraAttributesAfter = [];
    public $template = '{identification}{status}{role}{password}';

    /**
     * 
     */
    public function init()
    {

        parent::init();

        //Throw an Exception if model is not provided
        if (!isset($this->model)) {
            
        }

        if (empty($this->roleDropdownData)) {
            //Get Roles assigned to current user
            $userRoles = \Yii::$app->authManager->getRolesByUser(\Yii::$app->user->id);

            foreach ($userRoles as $role) {
                $this->roleDropdownData[$role['name']] = $role['name'];
                foreach (\Yii::$app->authManager->getChildren($role['name']) as $childRole) {
                    $this->roleDropdownData[$childRole['name']] = $childRole['name'];
                }
            }
        }
    }

    /**
     * 
     */
    public function run()
    {
        if ($this->enable) {
            //Begin ActtiveForm widget
            $this->form = ActiveForm::begin([
                        'id' => 'create-user',
                        'options' => [
                            'class' => 'form',
                            'enctype' => 'multipart/form-data'
                        ],
            ]);

            $this->_setupFields();
            echo Html::submitButton('Submit', ['class' => 'btn btn-primary']);
            //Begin ActtiveForm widget
            ActiveForm::end();
        }
    }

    private function _setupFields()
    {
        $fields = [];
        preg_match_all('/{(.*?)}/', $this->template, $fields);
        array_map(function($caller) {

            if (isset($this->extraAttributesBefore[$caller])) {
                $this->extraAttributesBefore[$caller]($this->model, $this->form);
            }
            $setupFn = '_setup' . ucfirst($caller) . 'Fields';
            $this->$setupFn();
            if (isset($this->extraAttributesAfter[$caller])) {
                $this->extraAttributesBefore[$caller]($this->model, $this->form);
            }
        }, $fields[1]);
    }

    private function _setupIdentificationFields()
    {

        /* Check if username column exists in corresponding AR database table */
        if ($this->model->hasAttribute('username')) {
            echo $this->form->field($this->model, 'username')->input('username');
        }
        echo $this->form->field($this->model, 'email')->input('email');
    }

    private function _setupStatusFields()
    {
        if ($this->model->hasAttribute('status') && $this->enableStatusDropdown) {
            echo $this->form->field($this->model, 'status')->dropDownList($this->statusDropdownData, ['prompt' => 'Select Status Value']);
        }
    }

    private function _setupRoleFields()
    {
        if ($this->enableRBAC && !empty($this->roleDropdownData)) {
            echo $this->form->field($this->model, 'roles')->widget(Select2::classname(), [
                'data' => $this->roleDropdownData,
                'language' => Yii::$app->language,
                'options' => ['placeholder' => 'Select Roles ...', 'multiple' => $this->multiRole],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
        }
        if ($this->enableRBAC && empty($this->roleDropdownData)) {
            echo 'Error: RBAC System has no roles defined' . '<br>';
        }
    }

    /**
     * 
     */
    private function _setupPasswordFields()
    {
        if (!$this->forcePasswordGeneration) {
            if ($this->enablePasswordGeneration) {
                echo $this->form->field($this->model, 'generatePassword')->checkBox(['attribute' => 'generatePassword', 'id' => 'generate-password', 'onclick' => "this.checked ?  $('#password-fields').hide() : $('#password-fields').show()",
                    'format' => 'boolean']);
            }

            echo '<div id="password-fields" style="display:';
            if ($this->model->generatePassword) {
                echo 'none"';
            } else {
                echo 'block"';
            }
            echo '>';

            echo $this->form->field($this->model, 'password')->input('password')->hint('Password should be within A-Za-z0-9')->label('Password');
            echo $this->form->field($this->model, 'password_confirm')->input('password')->label('Confirm Password');
            echo '</div>';
        }
    }

    private function _setupExtraAttributes($attribute, $before = FALSE)
    {
        //Get callback function from configuration
        $extra = 'extraAttributes' . ($before == TRUE ? 'Before' : 'After');
        $fn = $this->$extra[$attribute];

        if (isset($fn)) {
            $fn($this->model, $this->form);
        }
    }

}
