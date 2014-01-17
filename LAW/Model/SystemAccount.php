<?php
//Create by MySQL Model generator

class XP_Model_SystemAccount extends System_Lib_DataObject
{
    const ID = 'id';
    const NAME = 'name';
    const EMAIL = 'email';
    const MOBILE = 'mobile';
    const QQ = 'qq';
    const PASSWORD = 'password';
    const BEGIN_DATE = 'beginDate';
    const END_DATE = 'endDate';
    const PARENT_ID = 'parentId';
    const NOTE = 'note';
    const STATUS = 'status';
    const TYPE = 'type';
    const OPERATOR = 'operator';
    const CREATE_TIME = 'createTime';
    const UPDATE_TIME = 'updateTime';

    public $id;
    public $name;
    public $email;
    public $mobile;
    public $qq;
    public $password;
    public $beginDate;
    public $endDate;
    public $parentId;
    public $note;
    public $status;
    public $type;
    public $operator;
    public $createTime;
    public $updateTime;

    public static function getMapping()
    {
        return array(
            'table' => 'system_account',
            'key' => self::ID,
            'columns' => array(
                self::ID => 'id',
                self::NAME => 'name',
                self::EMAIL => 'email',
                self::MOBILE => 'mobile',
                self::QQ => 'qq',
                self::PASSWORD => 'password',
                self::BEGIN_DATE => 'beginDate',
                self::END_DATE => 'endDate',
                self::PARENT_ID => 'parentId',
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
                self::EMAIL => 'string',
                self::MOBILE => 'string',
                self::QQ => 'string',
                self::PASSWORD => 'string',
                self::BEGIN_DATE => 'string',
                self::END_DATE => 'string',
                self::PARENT_ID => 'int',
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
        return $config['system_account'];
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

