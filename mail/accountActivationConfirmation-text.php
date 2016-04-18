<?php
/* @var $this yii\web\View */
/* @var $account common\models\User */

$confirmationLink = Yii::$app->urlManager->createAbsoluteUrl(['/user/account/request-password-reset']);
?>
Dear Member,

Your account has been activated. You can confirm your account by performing the required actions through the link below:  


<?= $confirmationLink ?>
