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
        <?php
        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                'email:email', // title attribute (in plain text)
                'created_at:datetime', // creation date formatted as datetime
                'updated_at:datetime', // creation date formatted as datetime
            ],
        ]);

        echo Html::a('Reset Password', ['request-password-reset', 'id' => $model->id], ['class' => 'btn btn-success'])

        /*
          Modal::begin([
          'header' => '<h4>Hello world</h4>',
          'toggleButton' => ['label' => 'Generate Tole,'],
          ]);
          echo 'Testing';
          Modal::end();
         * 
         */
        ?>

    </div>

</div>