<?php

use yii\helpers\Html;

$columns = array_merge([[
'class' => yii\grid\ActionColumn::className(),
 'template' => '{delete}',
 'buttons' => [

    'delete' => function ($url, $model, $key) {
        $options = [
            'title' => Yii::t('yii', 'Delete'),
            'aria-label' => Yii::t('yii', 'Delete'),
            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
            'data-method' => 'post',
            'data-pjax' => '0',]
        ;
        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete-token', 'id' => $model['id']], $options);
    }]
// you may configure additional properties here
            ]]
                , [ 'identifier','tokenMask']);

        $config = [
            'dataProvider' => $dataProvider,
            'columns' => $columns,
        ];

        echo yii\grid\GridView::widget($config);
        