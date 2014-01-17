<?php
//Create by MySQL Model generator

class XP_Model_Keyword extends System_Lib_DataObject
{
    const ID = 'id';
    const PUBLIC_ACCOUNT_ID = 'publicAccountId';
    const NAME = 'name';
    const SECONDARY_NAME = 'secondaryName';
    const MATCH_TYPE = 'matchType';
    const CONTENT = 'content';
    const NOTE = 'note';
    const STATUS = 'status';
    const TYPE = 'type';
    const OPERATOR = 'operator';
    const CREATE_TIME = 'createTime';
    const UPDATE_TIME = 'updateTime';

    public $id;
    public $publicAccountId;
    public $name;
    public $secondaryName;
    public $matchType;
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
            'table' => 'keyword',
            'key' => self::ID,
            'columns' => array(
                self::ID => 'id',
                self::PUBLIC_ACCOUNT_ID => 'publicAccountId',
                self::NAME => 'name',
                self::SECONDARY_NAME => 'secondaryName',
                self::MATCH_TYPE => 'matchType',
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
                self::NAME => 'string',
                self::SECONDARY_NAME => 'string',
                self::MATCH_TYPE => 'int',
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
        return $config['keyword'];
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

