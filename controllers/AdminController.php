<?php

namespace humanized\user\controllers;

use yii\web\Controller;
use humanized\user\models\common\User;
use humanized\user\models\common\UserSearch;

/**
 * 
 */
class AdminController extends Controller {
    /**
     * =========================================================================
     *                              Protected Actions 
     *              Actions subject to module permission configuration
     * =========================================================================
     */

    /**
     * 
     * @return mixed
     */
    public function actionIndex()
    {
        //Account Creation Model
        $model = new User(['scenario' => User::SCENARIO_ADMIN]);
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $model = new User(['scenario' => User::SCENARIO_ADMIN]); //reset model
        }
        //Account Search Model
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        //
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
