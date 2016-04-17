<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$confirmationLink = Yii::$app->urlManager->createAbsoluteUrl(['/user/account/request-password-reset', 'id' => $account->id]);
?>
<div class="approve-account">

    <p>Dear Member,</p>

    <p>Your account has been activated. You can confirm your account by performing the required actions through the link below:  </p>


    <p><?= Html::a(Html::encode($confirmationLink), $confirmationLink) ?></p>
</div>
