<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$approveLink = Yii::$app->urlManager->createAbsoluteUrl(['/user/account/index', 'id' => $account->id]);
?>
<div class="approve-account">

    <p>Dear Administrator,</p>

    <p>An account has been registered on behalf of <?= $account->email ?> and is pending approval.</p>
    <p>You can perform the required action by following the link below:</p>

    <p><?= Html::a(Html::encode($approveLink), $approveLink) ?></p>
</div>
