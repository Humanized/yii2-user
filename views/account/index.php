<?php

use yii\widgets\DetailView;
use yii\bootstrap\Modal;
use yii\bootstrap\Html;

$this->params['breadcrumbs'][] = 'User Administration';
$this->params['breadcrumbs'][] = 'Accounts';
?>
<div class = "row">
    <aside class = "col-md-4">
        <div class = "well">
            <blockquote><span class = "glyphicon glyphicon-user"></span> User Administration</blockquote>
            <?=
            $this->render('/admin/_aside')
            ?>
        </div>

    </aside>

    <div class="col-md-8">
        <?=
        humanized\user\components\AccountDetails::widget([
            'model' => $model,
            'enableRBAC' => \Yii::$app->controller->module->params['enableRBAC'],
            'displayRBACMode' => \humanized\user\components\AccountDetails::DISPLAY_ROLE_ALL,
            'canVerifyAccount' => \Yii::$app->controller->module->params['permissions']['verify.account'],
            'canDeleteAccount' => \Yii::$app->controller->module->params['permissions']['delete.account'],
            'displayCreatedAt' => (\Yii::$app->controller->module->params['displayTimestamps'] || \Yii::$app->controller->module->params['displayCreatedAt']),
            'displayUpdatedAt' => (\Yii::$app->controller->module->params['displayTimestamps'] || \Yii::$app->controller->module->params['displayUpdatedAt']),
        ])
        ?>


    </div>

</div>
