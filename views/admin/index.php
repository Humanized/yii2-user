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
            humanized\user\components\AccountCreateForm::widget([
                'model' => $model,
                'enableRBAC' => TRUE,
                'statusDropdownData' => \humanized\user\components\GUIHelper::getStatusList()])
            ?>

        </div>   
    </aside>

    <div class="col-md-8">
        <?=
        humanized\user\components\AccountGrid::widget([
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ])
        ?>
    </div>
</div>