<?php
//Create by MySQL Model generator

class XP_Model_Menu extends System_Lib_DataObject
{
    const ID = 'id';
    const PUBLIC_ACCOUNT_ID = 'publicAccountId';
    const NAME = 'name';
    const PARENT_ID = 'parentId';
    const KEYWORD = 'keyword';
    const KEY = 'key';
    const SHOW_INDEX = 'showIndex';
    const NOTE = 'note';
    const STATUS = 'status';
    const TYPE = 'type';
    const OPERATOR = 'operator';
    const CREATE_TIME = 'createTime';
    const UPDATE_TIME = 'updateTime';

    public $id;
    public $publicAccountId;
    public $name;
    public $parentId;
    public $keyword;
    public $key;
    public $showIndex;
    public $note;
    public $status;
    public $type;
    public $operator;
    public $createTime;
    public $updateTime;

    public static function getMapping()
    {
        return array(
            'table' => 'menu',
            'key' => self::ID,
            'columns' => array(
                self::ID => 'id',
                self::PUBLIC_ACCOUNT_ID => 'publicAccountId',
                self::NAME => 'name',
                self::PARENT_ID => 'parentId',
                self::KEYWORD => 'keyword',
                self::KEY => 'key',
                self::SHOW_INDEX => 'showIndex',
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
                self::PARENT_ID => 'int',
                self::KEYWORD => 'string',
                self::KEY => 'string',
                self::SHOW_INDEX => 'int',
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
        return $config['menu'];
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

