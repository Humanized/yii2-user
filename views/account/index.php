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
            'model' => $model
        ])
        ?>


    </div>

</div>