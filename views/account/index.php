<div class="row">
    <aside class="col-md-2">
        <div class="well">
            <blockquote><span class="glyphicon glyphicon-user"></span> User Administration</blockquote>
            <?= $this->render('_aside') ?>
        </div>

    </aside>

    <div class="col-md-8">
        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                'email:email', // title attribute (in plain text)
                'created_at:datetime', // creation date formatted as datetime
                'updated_at:datetime', // creation date formatted as datetime
            ],
        ]);
        ?>
    </div>
    <div class="col-md-2">
        <div class="well">
            <blockquote><span class="glyphicon glyphicon-plus"></span> Create New User</blockquote>
            <?= $this->render('_create', ['model' => $model]) ?>
        </div>       
    </div>
</div>