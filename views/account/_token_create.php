<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

//$form = ActiveForm::begin(); //Default Active Form begin
$form = ActiveForm::begin([
            'id' => 'create-authentication-token',
            'options' => [
                'class' => 'form',
                'enctype' => 'multipart/form-data'
            ],
        ]);

echo $form->field($model, 'label');



echo Html::submitButton('Submit', ['class' => 'btn btn-primary']);
ActiveForm::end();

