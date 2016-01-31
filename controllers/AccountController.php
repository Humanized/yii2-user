<?php

namespace humanized\user\controllers;

use yii\web\Controller;
use humanized\user\models\common\User;
use humanized\user\models\common\AuthenticationToken;
use humanized\user\models\common\PasswordResetRequest;
use humanized\user\models\gui\LoginForm;

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
        \Yii::$app->user->logout();

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
        $model->scenario = 'signup';
        $model->generatePassword = FALSE;
        if ($model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                if (\Yii::$app->getUser()->login($model)) {
                    return $this->goHome();
                }
            }
        }
        return $this->render('signup', [
                    'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset($id = NULL)
    {
        $model = new PasswordResetRequest();
        if (isset($id) ? $model->loadMail($id) : $model->load(\Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                \Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return isset($id) ? $this->redirect(['index', 'id' => $id]) : $this->goHome();
            } else {
                \Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }
        return $this->render('requestPasswordResetToken', [
                    'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
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

    public function actionTokens($id)
    {
        if (!\Yii::$app->controller->module->params['enableTokenAuthentication']) {
            throw new \yii\web\NotFoundHttpException('Page not found.');
        }

        $model = new AuthenticationToken([
            'scenario' => AuthenticationToken::SCENARIO_TOKEN_GENERATION,
            'user_id' => $id
        ]);

        if ($model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                \Yii::$app->session->setFlash('success', "<strong>Token Generated Successfully (Copy it Now):</strong><br>" . $model->token);
            } else {
                //         \Yii::$app->session->setFlash('error', \Yii::$app->request->post());
            }
            $model = new AuthenticationToken(['scenario' => AuthenticationToken::SCENARIO_TOKEN_GENERATION, 'user_id' => $id]); //reset model
        }
        $searchModel = new AuthenticationToken(['user_id' => $id]);
        $dataProvider = $searchModel->search($id);

        return $this->render('token', [
                    'model' => $model,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDeleteToken($id)
    {
        if (!\Yii::$app->controller->module->params['enableTokenAuthentication']) {
            throw new \yii\web\NotFoundHttpException('Page not found.');
        }
        $token = AuthenticationToken::findOne($id);
        $caller = $token->user_id;
        $token->delete();
        $this->redirect(['tokens', 'id' => $caller]);
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
