<?php
//Create by MySQL Model generator

class XP_Model_MessageRecord extends System_Lib_DataObject
{
    const ID = 'id';
    const FROM_USER = 'fromUser';
    const TO_USER = 'toUser';
    const MESSAGE_TYPE = 'messageType';
    const CONTENT = 'content';
    const NOTE = 'note';
    const STATUS = 'status';
    const TYPE = 'type';
    const OPERATOR = 'operator';
    const CREATE_TIME = 'createTime';
    const UPDATE_TIME = 'updateTime';

    public $id;
    public $fromUser;
    public $toUser;
    public $messageType;
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
            'table' => 'message_record',
            'key' => self::ID,
            'columns' => array(
                self::ID => 'id',
                self::FROM_USER => 'fromUser',
                self::TO_USER => 'toUser',
                self::MESSAGE_TYPE => 'messageType',
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
                self::FROM_USER => 'string',
                self::TO_USER => 'string',
                self::MESSAGE_TYPE => 'int',
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
        return $config['message_record'];
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

