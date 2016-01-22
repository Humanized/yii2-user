<?php

namespace humanized\user\controllers;

use yii\web\Controller;
use humanized\user\models\gui\LoginForm;
use humanized\user\models\gui\SignupForm;
use humanized\user\models\common\User;
use humanized\user\models\common\UserSearch;

class AccountController extends Controller {

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        if (!\Yii::$app->controller->module->params['enableSignUp']) {
            throw new \yii\web\NotFoundHttpException('Page not found.');
        }
        $model = new User();
        if ($model->load(\Yii::$app->request->post())) {
            if ($user = $model->save()) {
                if (\Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
                    'model' => $model,
        ]);
    }

    public function actionIndex($id)
    {
        $model = User::findOne(['id' => $id]);


        return $this->render('index', [
                    'model' => $model,
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
