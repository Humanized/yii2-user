<?php

class UserSearch extends yii\db\ActiveRecord {
    
    //Search Filter Operations
    
  
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }
    
    public function search($params){
        
    }
    
   

}
