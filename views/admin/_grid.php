<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$columns = array_merge(
        (\Yii::$app->controller->module->params['emailOnly'] ? [] : ['username'])
        , ['email:email', 'created_at:datetime']);

$config = [
    'dataProvider' => $dataProvider,
    'columns' => $columns
];

echo yii\grid\GridView::widget($config);
