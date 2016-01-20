<?php

use yii\widgets\DetailView;
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

</div>