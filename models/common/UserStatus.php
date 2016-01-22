<?php

namespace humanized\user\models\common;

use yii\db\ActiveRecord;

class UserStatus extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

}
