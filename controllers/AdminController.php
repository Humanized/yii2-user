<?php

namespace humanized\user\controllers;

use yii\web\Controller;
use humanized\user\models\common\User;
use humanized\user\models\common\UserSearch;

class AdminController extends Controller {

    /**
     * 
     * @return mixed
     */
    public function actionIndex()
    {


        $model = new User(['scenario' => User::SCENARIO_ADMIN]);
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $model = new User(['scenario' => User::SCENARIO_ADMIN]); //reset model
        }

        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);


        return $this->render('index', [
                    'model' => $model,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider
        ]);
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function actionDelete($id)
    {
        $user = User::findOne(['id' => $id]);
        if (isset($user)) {
            $user->delete();
        }
        return $this->redirect(['index']);
    }

}
