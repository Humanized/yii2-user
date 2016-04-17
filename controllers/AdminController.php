<?php

namespace humanized\user\controllers;

use yii\web\Controller;
use humanized\user\models\common\UserSearch;
use humanized\user\models\notifications\PasswordResetRequest;

/**
 * 
 */
class AdminController extends Controller
{
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
        $identityClass = \Yii::$app->user->identityClass;

        //Account Creation Model (admin scenario)
        $model = new $identityClass(['scenario' => $identityClass::SCENARIO_DEFAULT]);
        if (\Yii::$app->controller->module->params['enableAdminVerification'] && !\Yii::$app->controller->module->params['permissions']['verify.account']) {
            $model->status = 0;
        }
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            //reset model on success
            $model = new $identityClass(['scenario' => $identityClass::SCENARIO_DEFAULT]);
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

        $identityClass = \Yii::$app->user->identityClass;
        $user = $identityClass::findOne(['id' => $id]);
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
    public function actionVerify($id, $alt = FALSE)
    {
        $identityClass = \Yii::$app->user->identityClass;
        $user = $identityClass::findOne(['id' => $id]);
        if (isset($user)) {
            if ($user->status == 0) {
                $user->status = 10;
                $model = new PasswordResetRequest();
                if (isset($id) ? $model->loadMail($id) : $model->load(\Yii::$app->request->post()) && $model->validate()) {
                    $model->sendEmail();
                }
            } else {
                $user->status = 0;
            }
            $user->save(false);
        }
        if ($alt == TRUE) {
            return $this->redirect(['account/index', 'id' => $id]);
        }
        return $this->redirect(['index']);
    }

}
