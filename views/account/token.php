<?php

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

        <div class = "well">
            <blockquote><span class = "glyphicon glyphicon-user"></span> Generate Token</blockquote>
            <?=
            $this->render('_token_create', ['model' => $model]);
            ?>
        </div>

    </aside>

    <div class="col-md-8">
        <?=
        $this->render('_tokens', [
            'dataProvider' => $dataProvider,
            'searchModel' => $model
        ])
        ?>
    </div>

</div>