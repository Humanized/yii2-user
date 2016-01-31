<?php

namespace humanized\user\models\common;

use humanized\user\models\common\User;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * Password reset request form
 */
class AuthenticationToken extends ActiveRecord {

    const SCENARIO_TOKEN_GENERATION = 'generate';

    public $token;

    public function rules()
    {
        return [
            ['user_id', 'required'],
            ['label', 'required', 'on' => [self::SCENARIO_TOKEN_GENERATION]],
        ];
    }

    public function search()
    {
        $query = new \yii\db\Query;
        $maskExp = new \yii\db\Expression("'***************************************************'");
        $query->select(['id' => 'id', 'label' => 'label', 'tokenMask' => $maskExp]);
        $query->from = ['authentication_token'];
        $query->where(['user_id' => $this->user_id]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function beforeValidate()
    {
        $this->token = \Yii::$app->security->generateRandomString(100);
        $this->token_hash = \Yii::$app->security->generatePasswordHash($this->token);
        return parent::beforeValidate();
    }

}
