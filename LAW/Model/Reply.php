<?php
//Create by MySQL Model generator

class XP_Model_Reply extends System_Lib_DataObject
{
    const ID = 'id';
    const PUBLIC_ACCOUNT_ID = 'publicAccountId';
    const REPLY_TYPE = 'replyType';
    const CONTENT = 'content';
    const NOTE = 'note';
    const STATUS = 'status';
    const TYPE = 'type';
    const OPERATOR = 'operator';
    const CREATE_TIME = 'createTime';
    const UPDATE_TIME = 'updateTime';

    public $id;
    public $publicAccountId;
    public $replyType;
    public $content;
    public $note;
    public $status;
    public $type;
    public $operator;
    public $createTime;
    public $updateTime;

    public static function getMapping()
    {
        return array(
            'table' => 'reply',
            'key' => self::ID,
            'columns' => array(
                self::ID => 'id',
                self::PUBLIC_ACCOUNT_ID => 'publicAccountId',
                self::REPLY_TYPE => 'replyType',
                self::CONTENT => 'content',
                self::NOTE => 'note',
                self::STATUS => 'status',
                self::TYPE => 'type',
                self::OPERATOR => 'operator',
                self::CREATE_TIME => 'createTime',
                self::UPDATE_TIME => 'updateTime'
            ),
            'columnTypes' => array(
                self::ID => 'int',
                self::PUBLIC_ACCOUNT_ID => 'int',
                self::REPLY_TYPE => 'int',
                self::CONTENT => 'string',
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
        return $config['reply'];
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

