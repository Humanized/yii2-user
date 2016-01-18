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
/* ADD FORM FIELDS */
echo $form->field($model, 'email')->input('email');
if (\Yii::$app->controller->module->params['enableStatusCodes']) {
    echo $form->field($model, 'status')->dropDownList(\humanized\user\components\GUIHelper::getStatusList(), ['prompt' => 'Select Status Value']);
}
//echo $form->field($model, 'status')->input('status');

echo Html::submitButton('Submit', ['class' => 'btn btn-primary']);
ActiveForm::end();

