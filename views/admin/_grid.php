<?php

use yii\helpers\Html;

$columns = array_merge([[
'class' => yii\grid\ActionColumn::className(),
 'template' => '{view} {verify} {delete}',
 'buttons' => [

    'view' => function ($url, $model, $key) {
        $options = [
            'title' => Yii::t('yii', 'Account Details'),
            'aria-label' => Yii::t('yii', 'Account Details'),
            'data-pjax' => '0'];

        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/user/account/index', 'id' => $model['id']], $options);
    },
            'verify' => function ($url, $model, $key) {
        $inactive = ((int) $model['status'] == 0 ? TRUE : FALSE);
        $options = [
            'title' => Yii::t('yii', ($inactive == TRUE ? 'Enable Account' : 'Disable Account')),
            'aria-label' => Yii::t('yii', ($inactive == TRUE ? 'Enable Account' : 'Disable Account')),
            'data-pjax' => '0',
            'visible' => \Yii::$app->user->can(\Yii::$app->controller->module->params['permissions']['verify.account']),
            'hidden' => \Yii::$app->user->can(\Yii::$app->controller->module->params['permissions']['verify.account']),
        ];

        return Html::a('<span class="glyphicon glyphicon-' . ($inactive == TRUE ? 'play' : 'stop') . '"></span>', ['/user/admin/verify', 'id' => $model['id']], $options);
    },
            'delete' => function ($url, $model, $key) {
        $inactive = (int) $model['status'] == 0 ? TRUE : FALSE;
        $options = [
            'visible' => $inactive == TRUE?TRUE:FALSE && \Yii::$app->user->can(\Yii::$app->controller->module->params['permissions']['delete.account']),
            'hidden' => $inactive == TRUE?TRUE:FALSE && \Yii::$app->user->can(\Yii::$app->controller->module->params['permissions']['delete.account']),
            'title' => Yii::t('yii', 'Delete Account'),
            'aria-label' => Yii::t('yii', 'Delete Account'),
            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this account?'),
            'data-method' => 'post',
            'data-pjax' => '0',
        ];
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
        