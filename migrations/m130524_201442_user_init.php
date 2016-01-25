<?php

use yii\db\Migration;

class m130524_201442_user_init extends Migration {

    private $_module = NULL;
    private $_tableOptions = NULL;

    public function init()
    {
        parent::init();

       

        $this->_module = \Yii::$app->getModule('user');

        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $this->_tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
    }

    public function up()
    {
        $userAttributes = [
            'id' => $this->primaryKey(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ];
        if ($this->_module->params['enableUserName']) {
            $attribs['username'] = $this->string()->notNull()->unique();
        }
        if ($this->_module->params['enableTokenAuthentication']) {
            $attribs['auth_token'] = $this->string()->notNull();
        }
        if ($this->_module->params['enableStatusCodes']) {
            $attribs['status'] = $this->smallInteger()->notNull()->defaultValue($this->_module->params['defaultStatusCode']);
            if (!$this->_setupStatusCodeTable()) {
                return FALSE;
            }
        }
        $this->createTable('{{%user}}', $userAttributes, $this->_tableOptions);
        if (isset($this->_module->params['statusCodeTable'])) {
            $this->addForeignKey('fk_user_status', '{{%user}}', 'status', $this->_module->params['statusCodeTable'], 'id');
        }
    }

    private function _setupStatusCodeTable()
    {
        $statusTable = $this->_module->params['statusCodeTable'];
        if (isset($statusTable)) {
            $status_id = $this->_module->params['statusCodeIdAttribute'];
            $status_name = $this->_module->params['statusCodeNameAttribute'];
            if (!(isset($status_id) && isset($status_name))) {
                return FALSE;
            }
            $this->createTable($statusTable, [$status_id => $this->smallInteger(), $status_name => $this->string(20)], $this->_tableOptions);
            $this->addPrimaryKey('pk_status', $statusTable, $status_id);
        }
        return TRUE;
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }

}
