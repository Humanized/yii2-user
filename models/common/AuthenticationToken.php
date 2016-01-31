<?php

namespace humanized\user\models\common;

use humanized\user\models\common\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Password reset request form
 */
class AuthenticationToken extends Model {

    public $user_id;
    public $identifier;
    public $token;

    public function search()
    {
        $query = new \yii\db\Query;
        $maskExp = new \yii\db\Expression("'********************************************************************************************'");
        $query->select(['id' => 'id', 'identifier' => 'title', 'tokenMask' => $maskExp]);
        $query->from = ['authentication_token'];
        $query->where(['user_id' => $this->user_id]);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

}
