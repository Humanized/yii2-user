<?php
$this->params['breadcrumbs'][] = 'User Administration';
$this->params['breadcrumbs'][] = 'User Management';
?>
<div class="row">
    <aside class="col-md-4">
        <div class="well">
            <blockquote><span class="glyphicon glyphicon-user"></span> User Administration</blockquote>
            <?= $this->render('_aside') ?>
        </div>
        <div class="well">
            <blockquote><span class="glyphicon glyphicon-plus"></span> Create New User</blockquote>

            <?=
            humanized\user\components\AccountCreateForm::widget(array_merge([
                'model' => $model,
                'enableRBAC' => \Yii::$app->controller->module->params['enableRBAC'],
                'enable' => \Yii::$app->controller->module->params['permissions']['create.account'],
                'enableStatusDropdown' => \Yii::$app->controller->module->params['enableStatusCodes'] && \Yii::$app->controller->module->params['permissions']['verify.account'],
                'statusDropdownData' => \humanized\user\components\GUIHelper::getStatusList(),
                'forcePasswordGeneration' => \Yii::$app->controller->module->params['enableUserVerification'],
                            ], \Yii::$app->controller->module->params['formOptions']))
            ?>

        </div>   
    </aside>

    <div class="col-md-8">
        <?=
        humanized\user\components\AccountGrid::widget(array_merge([
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'enable' => \Yii::$app->controller->module->params['permissions']['access.dashboard'],
            'canViewAccount' => \Yii::$app->controller->module->params['permissions']['view.account'],
            'canVerifyAccount' => \Yii::$app->controller->module->params['permissions']['verify.account'],
            'canDeleteAccount' => \Yii::$app->controller->module->params['permissions']['delete.account'],
            'displayCreatedAt' => (\Yii::$app->controller->module->params['displayTimestamps'] || \Yii::$app->controller->module->params['displayCreatedAt']),
            'displayUpdatedAt' => (\Yii::$app->controller->module->params['displayTimestamps'] || \Yii::$app->controller->module->params['displayUpdatedAt']),
            'displayStatusColumn' => \Yii::$app->controller->module->params['enableStatusCodes'],
            'enableRBAC' => \Yii::$app->controller->module->params['enableRBAC'],
                        ], \Yii::$app->controller->module->params['gridOptions']))
        ?>
    </div>
</div>