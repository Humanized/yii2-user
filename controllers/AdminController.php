<?php

namespace humanized\user\controllers;

use yii\web\Controller;

class AdminController extends Controller {

    public function actionIndex()
    {

        $this->render(['index']);
    }

}
