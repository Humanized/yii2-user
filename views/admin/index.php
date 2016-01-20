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
            <?= $this->render('_create', ['model' => $model]) ?>
        </div>   
    </aside>

    <div class="col-md-8">
        <?=
        $this->render('_grid', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ])
        ?>
    </div>
</div>