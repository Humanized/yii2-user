<?php

use yii\db\Migration;

class m130524_201442_user_init extends Migration {

    private $_params = NULL;
    private $_tableOptions = NULL;

    public function init()
    {
        parent::init();



        $this->_params = \Yii::$app->getModule('user')->params;

        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $this->_tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
    }

    public function up()
    {


        $attribs = [
            'id' => $this->primaryKey(),
            'email' => $this->string()->notNull()->unique(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ];

        if ($this->_params['enableUserName']) {
            $attribs['username'] = $this->string()->notNull()->unique();
        }
        if ($this->_params['enableAdminVerification']) {
            $attribs['enable_notification'] = $this->boolean(FALSE)->notNull();
        }
        if ($this->_params['enableStatusCodes']) {

            $attribs['status'] = $this->smallInteger()->notNull()->defaultValue($this->_params['defaultStatusCode']);
            if (!$this->_setupStatusCodeTable()) {
                return FALSE;
            }
        }
        $this->createTable('{{%user}}', $attribs, $this->_tableOptions);

        if (isset($this->_params['enableTokenAuthentication'])) {
            $this->_setupAuthenticationTokenTable();
        }

        if (isset($this->_params['statusCodeTable'])) {
            $this->addForeignKey('fk_user_status', '{{%user}}', 'status', $this->_params['statusCodeTable'], 'id');
        }
    }

    private function _setupAuthenticationTokenTable()
    {
        $this->createTable('authentication_token', [
            'id' => $this->primaryKey(),
            'label' => $this->string(25)->notNull(),
            'token_hash' => $this->string()->unique()->notNull(),
            'user_id' => $this->integer()->notNull()
                ], $this->_tableOptions);
        $this->addForeignKey('fk_authentication_token_user', 'authentication_token', 'user_id', '{{%user}}', 'id');
    }

    private function _setupStatusCodeTable()
    {
        $statusTable = $this->_params['statusCodeTable'];
        if (isset($statusTable)) {
            $status_id = $this->_params['statusCodeIdAttribute'];
            $status_name = $this->_params['statusCodeNameAttribute'];
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
