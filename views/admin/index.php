<div class="row">
    <aside class="col-md-2">
        <div class="well">
            <blockquote><span class="glyphicon glyphicon-user"></span> User Administration</blockquote>
            <?= $this->render('_aside') ?>
        </div>
        <div class="well">
            <blockquote><span class="glyphicon glyphicon-plus"></span> Create New User</blockquote>
            <?= $this->render('_create', ['model' => new \humanized\user\models\User()]) ?>
        </div>
    </aside>

    <div class="col-md-6">
        <?=
        $this->render('_grid', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ])
        ?>
    </div>
    <div class="col-md-4">
        Search        
    </div>
</div>