<?php
/**
 * Created by PhpStorm.
 * User: Nurbek
 * Date: 2/22/16
 * Time: 5:02 PM
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="user-form">

<?php $form = ActiveForm::begin([
    //'enableAjaxValidation'=>true,
    //'enableClientValidation'=>false,
    'options' => [ 'enctype' => 'multipart/form-data'],
]); ?>

    <?=$form->errorSummary($model);?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'password_confirm')->passwordInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>