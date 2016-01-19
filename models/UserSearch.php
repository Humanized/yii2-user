<?php

namespace humanized\user\models;

use yii\data\ActiveDataProvider;

class UserSearch extends User {

    //Search Filter Parameters
    public $userFilter = [];
    public $statusFilter = [];

    public function search($params)
    {
        $unfiltered = !($this->load($params) && $this->validate());
        $query = new \yii\db\Query;
       
        $query->from = ['{{%user}}'];


        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

}
