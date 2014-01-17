<?php
//Create by MySQL Model generator

class XP_Model_PublicAccount extends System_Lib_DataObject
{
    const ID = 'id';
    const NAME = 'name';
    const ORIGINAL_ID = 'originalId';
    const WEIXIN = 'weixin';
    const SYSTEM_ACCOUNT_ID = 'systemAccountId';
    const WEIXIN_PAY = 'weixinPay';
    const BEGIN_DATE = 'beginDate';
    const END_DATE = 'endDate';
    const APP_ID = 'appId';
    const APP_SECRET = 'appSecret';
    const URL = 'url';
    const TOKEN = 'token';
    const NOTE = 'note';
    const STATUS = 'status';
    const TYPE = 'type';
    const OPERATOR = 'operator';
    const CREATE_TIME = 'createTime';
    const UPDATE_TIME = 'updateTime';
	const CUSTOMER_SERVICE='customerService';

    public $id;
    public $name;
    public $originalId;
    public $weixin;
    public $systemAccountId;
    public $weixinPay;
    public $beginDate;
    public $endDate;
    public $appId;
    public $appSecret;
    public $url;
    public $token;
    public $note;
    public $status;
    public $type;
    public $operator;
    public $createTime;
    public $updateTime;
	public $customerService;

    public static function getMapping()
    {
        return array(
            'table' => 'public_account',
            'key' => self::ID,
            'columns' => array(
                self::ID => 'id',
                self::NAME => 'name',
                self::ORIGINAL_ID => 'originalId',
                self::WEIXIN => 'weixin',
                self::SYSTEM_ACCOUNT_ID => 'systemAccountId',
                self::WEIXIN_PAY => 'weixinPay',
                self::BEGIN_DATE => 'beginDate',
                self::END_DATE => 'endDate',
                self::APP_ID => 'appId',
                self::APP_SECRET => 'appSecret',
                self::URL => 'url',
                self::TOKEN => 'token',
                self::NOTE => 'note',
                self::STATUS => 'status',
                self::TYPE => 'type',
                self::OPERATOR => 'operator',
                self::CREATE_TIME => 'createTime',
                self::UPDATE_TIME => 'updateTime',
				self::CUSTOMER_SERVICE=>'customerService'
            ),
            'columnTypes' => array(
                self::ID => 'int',
                self::NAME => 'string',
                self::ORIGINAL_ID => 'string',
                self::WEIXIN => 'string',
                self::SYSTEM_ACCOUNT_ID => 'int',
                self::WEIXIN_PAY => 'int',
                self::BEGIN_DATE => 'string',
                self::END_DATE => 'string',
                self::APP_ID => 'string',
                self::APP_SECRET => 'string',
                self::URL => 'string',
                self::TOKEN => 'string',
                self::NOTE => 'string',
                self::STATUS => 'int',
                self::TYPE => 'int',
                self::OPERATOR => 'int',
                self::CREATE_TIME => 'string',
                self::UPDATE_TIME => 'string',
				self::CUSTOMER_SERVICE=>'int'
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
        return $config['public_account'];
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

