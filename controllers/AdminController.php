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
     *        Actions subject to module permission configuration (all actually)
     * =========================================================================
     */

    /**
     * User Administration Dashboard
     * Module permission check requires either user-admin or user-group-admin
     * priviliges to continue.
     * @return mixed
     */
    public function actionIndex()
    {
        //Account Creation Model (admin scenario)
        $model = new User(['scenario' => User::SCENARIO_ADMIN]);
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            //reset model on success
            $model = new User(['scenario' => User::SCENARIO_ADMIN]);
        }
        //Account Search Model
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        //Render index view file
        return $this->render('index', [
                    'model' => $model,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider
        ]);
    }

    /**
     * User Account Deletion
     * Module permission check requires either user-admin or user-group-admin
     * priviliges to continue.
     * @return mixed
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

    /**
     * User Account Verification
     * Module permission check requires either user-admin or user-group-admin
     * priviliges to continue.
     * @return mixed
     * @param type $id
     * @return type
     */
    public function actionVerify($id)
    {
        $user = User::findOne(['id' => $id]);
        if (isset($user)) {
            if ((int) $user->status == 0) {
                $user->status = 10;
            } else {
                $user->status = 0;
            }
            $user->save(false);
        }
        return $this->redirect(['index']);
    }

}
