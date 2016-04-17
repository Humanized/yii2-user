<?php

/* @var $this yii\web\View */
/* @var $account common\models\User */

$approveLink = Yii::$app->urlManager->createAbsoluteUrl(['/user/account/index', 'id' => $account->id]);
?>
Dear Administrator,

An account has been registered on behalf of <?= $account->email ?> and is pending approval.

You can perform the required action by following the link below:

<?= $approveLink ?>
