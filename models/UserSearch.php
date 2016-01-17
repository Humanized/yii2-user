<?php

class UserSearch extends yii\db\ActiveRecord {

    //Search Filter Parameters
    public $userFilter = [];
    public $statusFilter = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    public function search($params)
    {
        
    }

}
