<?php
/* @var $this yii\web\View */
/* @var $account common\models\User */

$confirmationLink = Yii::$app->urlManager->createAbsoluteUrl(['/user/account/reset-password', 'id' => $account->id]);
?>
Dear Member,

Your account has been activated. You can confirm your account by clicking the link below:  


<?= $confirmationLink ?>
