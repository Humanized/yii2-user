<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['/user/account/reset-password', 'token' => $user->password_reset_token]);
?>
Dear Member,

Follow the link below to set your password::

<?= $resetLink ?>
