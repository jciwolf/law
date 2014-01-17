<?php
//Create by MySQL Model generator

class XP_Model_Module extends System_Lib_DataObject
{
    const ID = 'id';
    const NAME = 'name';
    const ALIAS = 'alias';
    const URL = 'url';
    const KEY = 'key';
    const PARENT_ID = 'parentId';
    const SHOW_INDEX = 'showIndex';
    const IS_VISIBLE = 'isVisible';
    const NOTE = 'note';
    const STATUS = 'status';
    const TYPE = 'type';
    const OPERATOR = 'operator';
    const CREATE_TIME = 'createTime';
    const UPDATE_TIME = 'updateTime';

    public $id;
    public $name;
    public $alias;
    public $url;
    public $key;
    public $parentId;
    public $showIndex;
    public $isVisible;
    public $note;
    public $status;
    public $type;
    public $operator;
    public $createTime;
    public $updateTime;

    public static function getMapping()
    {
        return array(
            'table' => 'module',
            'key' => self::ID,
            'columns' => array(
                self::ID => 'id',
                self::NAME => 'name',
                self::ALIAS => 'alias',
                self::URL => 'url',
                self::KEY => 'key',
                self::PARENT_ID => 'parentId',
                self::SHOW_INDEX => 'showIndex',
                self::IS_VISIBLE => 'isVisible',
                self::NOTE => 'note',
                self::STATUS => 'status',
                self::TYPE => 'type',
                self::OPERATOR => 'operator',
                self::CREATE_TIME => 'createTime',
                self::UPDATE_TIME => 'updateTime'
            ),
            'columnTypes' => array(
                self::ID => 'int',
                self::NAME => 'string',
                self::ALIAS => 'string',
                self::URL => 'string',
                self::KEY => 'string',
                self::PARENT_ID => 'int',
                self::SHOW_INDEX => 'int',
                self::IS_VISIBLE => 'int',
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
        return $config['module'];
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

