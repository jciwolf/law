<?php
//Create by MySQL Model generator

class XP_Model_Permission extends System_Lib_DataObject
{
    const ID = 'id';
    const ACCOUNT_ID = 'accountId';
    const MODULE_ID = 'moduleId';
    const NOTE = 'note';
    const STATUS = 'status';
    const TYPE = 'type';
    const OPERATOR = 'operator';
    const CREATE_TIME = 'createTime';
    const UPDATE_TIME = 'updateTime';

    public $id;
    public $accountId;
    public $moduleId;
    public $note;
    public $status;
    public $type;
    public $operator;
    public $createTime;
    public $updateTime;

    public static function getMapping()
    {
        return array(
            'table' => 'permission',
            'key' => self::ID,
            'columns' => array(
                self::ID => 'id',
                self::ACCOUNT_ID => 'accountId',
                self::MODULE_ID => 'moduleId',
                self::NOTE => 'note',
                self::STATUS => 'status',
                self::TYPE => 'type',
                self::OPERATOR => 'operator',
                self::CREATE_TIME => 'createTime',
                self::UPDATE_TIME => 'updateTime'
            ),
            'columnTypes' => array(
                self::ID => 'int',
                self::ACCOUNT_ID => 'int',
                self::MODULE_ID => 'int',
                self::NOTE => 'string',
                self::STATUS => 'int',
                self::TYPE => 'int',
                self::OPERATOR => 'int',
                self::CREATE_TIME => 'string',
                self::UPDATE_TIME => 'string'
            )
        );
    }

    /**
     *
     * @return
     */
    public static function getSourceConfig()
    {
        $config = System_Lib_App::app()->getConfig('dbConfig');
        return $config['permission'];
    }

    /**
     *
     * @return System_Lib_MysqlAccessor
     */
    public static function dataAccess()
    {
        return System_Lib_MysqlAccessor::useModel(get_class());
    }

    /**
     *
     * @return
     */
    public static function getDataAccessName()
    {
        return 'System_Lib_MysqlAccessor';
    }
}

