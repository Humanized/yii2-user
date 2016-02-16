<?php

use yii\helpers\Html;

$columns = array_merge([[
'class' => yii\grid\ActionColumn::className(),
 'template' => '{view} {delete}',
 'buttons' => [
    'view' => function ($url, $model, $key) {
        $options = [
            'title' => Yii::t('yii', 'View'),
            'aria-label' => Yii::t('yii', 'View'),
            'data-pjax' => '0']
        ;

        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/user/account/index', 'id' => $model['id']], $options);
    },
            'delete' => function ($url, $model, $key) {
        $options = [
            'title' => Yii::t('yii', 'Delete'),
            'aria-label' => Yii::t('yii', 'Delete'),
            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
            'data-method' => 'post',
            'data-pjax' => '0',]
        ;
        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model['id']], $options);
    }]
// you may configure additional properties here
            ]], (\Yii::$app->controller->module->params['enableStatusCodes'] ? [['label' => 'Active', 'format' => 'html', 'value' => function ($model, $key, $index, $column) {
                    $inactive = ((int) $model['status'] == 0 ? TRUE : FALSE);
                    return \humanized\user\components\GUIHelper::getStatusOutput($inactive);
            
                }]] : [])
                , (\Yii::$app->controller->module->params['enableUserName'] ? ['username'] : [])
                , [ 'email:email', 'created_at:datetime']);

        $config = [
            'dataProvider' => $dataProvider,
            'columns' => $columns,
        ];

        echo yii\grid\GridView::widget($config);
        