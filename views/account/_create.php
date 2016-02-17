<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

//$form = ActiveForm::begin(); //Default Active Form begin
$form = ActiveForm::begin([
            'id' => 'create-user',
            'options' => [
                'class' => 'form',
                'enctype' => 'multipart/form-data'
            ],
        ]);
/* Form Fields */
if (\Yii::$app->controller->module->params['enableUserName']) {
    echo $form->field($model, 'username')->input('username');
}
echo $form->field($model, 'email')->input('email');

if ($model->scenario != \humanized\user\models\common\User::SCENARIO_SIGNUP) {
    if (\Yii::$app->controller->module->params['enableRBAC']) {
        echo $form->field($model, 'roles')->widget(Select2::classname(), [
            'data' => array_map(function($r) {
                        return $r->name;
                    }, \Yii::$app->authManager->getRoles()),
            'language' => Yii::$app->language,
            'options' => ['placeholder' => 'Select Roles ...', 'multiple' => true],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        
    }
    if (\Yii::$app->controller->module->params['enableStatusCodes'] && \Yii::$app->controller->module->params['permissions']['verify.account']) {
        echo $form->field($model, 'status')->dropDownList(\humanized\user\components\GUIHelper::getStatusList(), ['prompt' => 'Select Status Value']);
    }
//Password Generation -- Forces when enableUserVerfication is set to TRUE
    if (!\Yii::$app->controller->module->params['enableUserVerification']) {
        echo $form->field($model, 'generatePassword')->checkBox(['attribute' => 'generatePassword', 'id' => 'generate-password', 'onclick' => "this.checked ?  $('#password-fields').hide() : $('#password-fields').show()",
            'format' => 'boolean']);
    }
}
?>
<div id="password-fields" style="display:<?= $model->generatePassword ? "none" : "block" ?>">
<?php
echo $form->field($model, 'password')->input('password')->hint('Password should be within A-Za-z0-9')->label('Password');
echo $form->field($model, 'password_confirm')->input('password')->label('Confirm Password')
?>
</div>
    <?php
    echo Html::submitButton('Submit', ['class' => 'btn btn-primary']);
    ActiveForm::end();

    