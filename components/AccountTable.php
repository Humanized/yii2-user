<?php

namespace humanized\user\components;

use humanized\user\models\common\User;

/**
 * Role Hierarchy Table
 * Data loader for simple linear rbac hierarchies
 */
class AccountTable extends \humanized\clihelpers\components\DataTable {

    public $debugPassword = '#!@dmin201x';
    public $debug = FALSE;
    public $modelClass = NULL;
    public $defaultRoles = [];
    public $defaultStatus = 10;

    public function __construct()
    {
        $this->modelClass = \Yii::$app->user->identityClass;
    }

    /**
     *
     * @var array[role-name=>['permissions'=>'']] 
     */
    public $records = [
    ];

    public static function load()
    {

        $class = get_called_class();
        echo 'Loading role hierarchy from file: ' . "$class \n";
        $instance = new $class();
        $user = \Yii::$app->user->identityClass;
        foreach ($instance->records as $record) {
            $instance->processRecord(array_merge(['moduleName' => 'user'], $record));
            $model = new $user();
            $model->setAttributes($record);
            $model->save();
        }
        echo 'Complete' . "\n";
    }

    public function processRecord(&$record)
    {
        if ($this->debug) {
            $record['generatePassword'] = FALSE;
            if (!isset($record['password'])) {
                $record['password'] = $this->debugPassword;
            }
            if (!isset($record['password_confirm'])) {
                $record['password_confirm'] = $record['password'];
            }
        } else {
            $record['generatePassword'] = TRUE;
        }
        if (!isset($record['status'])) {
            $record['status'] = $this->defaultStatus;
        }
        return;
    }

    public function unloadCondition($record)
    {
        return ['email' => $record['email']];
    }

}
