<?php

namespace humanized\user\controllers;

use yii\web\Controller;
use humanized\user\models\common\User;
use humanized\user\models\common\UserSearch;

class AdminController extends Controller {

    public function beforeAction($action)
    {
        if (\Yii::$app->user->isGuest) {
            throw new \yii\web\NotFoundHttpException('Page not found.');
        }


        $accessGranted = NULL;
        $error = 'User Module: accessAdmin parameter of permissions array is incorrectly set';

        $accessAdmin = $this->module->params['permissions']['accessAdmin'];
        switch (gettype($accessAdmin)) {
            case "boolean": {
                    $accessGranted = $accessAdmin;
                    break;
                }
            case "string": {
                    if ($this->module->params['enableRBAC']) {
                        $accessGranted = \Yii::$app->user->can($accessAdmin);
                    } else {
                        $error = 'User Module: RBAC not Enabled';
                    }
                    break;
                }
            case "array": {
                    $accessGranted = NULL;
                    break;
                }
            default: {
                    $accessGranted = NULL;
                    break;
                }
        }
        if (!isset($accessGranted)) {

            throw new \yii\web\BadRequestHttpException($error);
        }

        return $accessGranted && parent::beforeAction($action);
    }

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
