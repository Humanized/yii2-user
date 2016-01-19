<?php

namespace humanized\user\controllers;

use yii\web\Controller;
use humanized\user\models\User;
use humanized\user\models\UserSearch;

class AdminController extends Controller {

    public function actionIndex()
    {
        $model = new User();
        $model->generateAuthKey();
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $model = new User(); //reset model
        }

        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);


        return $this->render('index', [
                    'model' => $model,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider
        ]);
    }

    public function actionCreate()
    {
        
    }

    public function actionUpdate($id)
    {
        
    }

    public function actionView($id)
    {
        
    }

    public function actionDelete($id)
    {
        
    }

}
