<?php

use yii\db\Migration;
use humanized\user\models\common\User;

class m160222_114153_add_statistic_fields_to_user_table extends Migration
{

    public function safeUp()
    {
        $this->addColumn(User::tableName(), 'last_login', $this->dateTime());
        $this->addColumn(User::tableName(), 'login_count', $this->integer());
        $this->addColumn(User::tableName(), 'session_average', $this->integer());
        $this->addColumn(User::tableName(), 'session_total', $this->integer());
    }

    public function safeDown()
    {
        $this->dropColumn(User::tableName(), 'last_login');
        $this->dropColumn(User::tableName(), 'login_count');
        $this->dropColumn(User::tableName(), 'session_average');
        $this->dropColumn(User::tableName(), 'session_total');
    }
}
