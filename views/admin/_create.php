<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

//$form = ActiveForm::begin(); //Default Active Form begin
$form = ActiveForm::begin([
            'id' => 'create-user',
            'options' => [
                'class' => 'form',
                'enctype' => 'multipart/form-data'
            ],
        ]);
/* Form Fields */
if (!\Yii::$app->controller->module->params['emailOnly']) {
    echo $form->field($model, 'username')->input('username');
}
echo $form->field($model, 'email')->input('email');
if (\Yii::$app->controller->module->params['enableStatusCodes']) {
    echo $form->field($model, 'status')->dropDownList(\humanized\user\components\GUIHelper::getStatusList(), ['prompt' => 'Select Status Value']);
}
//Optional Password Autogeneration
echo $form->field($model, 'generatePassword')->checkBox(['attribute' => 'generatePassword',
    'format' => 'boolean']);
?>
<div id="password-fields">
    <?php
    echo $form->field($model, 'password')->input('password')->hint('Password should be within A-Za-z0-9')->label('Password');
    echo $form->field($model, 'password_confirm')->input('password')->label('Confirm Password')
    ?>
</div>
<?php
echo Html::submitButton('Submit', ['class' => 'btn btn-primary']);
ActiveForm::end();

