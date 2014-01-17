<?php
/**
 * @DESCRIPTION
 * 
 * 
 * @MODIFY
 * 13-11-18 下午3:11 create file
 * 
 * @author Kevin.Hu, Inc. <huwenhua@group-net.cn>
 * @version v2.1
 */

class XP_Extension_FractaChevrolet_UserModel extends System_Lib_DataObject
{
	const ID                = 'id';
	const OPENID			= 'openid';
	const TICKET            = 'ticket';
	const NICKNAME			= 'nickname';
	const SEX 				= 'sex';
	const CITY 				= 'city';
	const PROVINCE 			= 'province';
	const COUNTRY			= 'country';
	const SUBSCRIBE			= 'subscribe';
	const SUBSCRIBE_TIME	= 'subscribeTime';
	const UNSUBSCRIBE_TIME	= 'unsubscribeTime';
	const CREATE_TIME		= 'createTime';

	public $id;
	public $openid;
	public $ticket;
	public $nickname;
	public $sex;
	public $city;
	public $province;
	public $country;
	public $subscribe;
	public $subscribeTime;
	public $unsubscribeTime;
	public $createTime;

	public static function getMapping()
	{
		return array(
			'table'       => 'fracta_chevrolet_user',
			'key'         => self::ID,
			'columns'     => array(
				self::ID                => 'id',
				self::OPENID 			=> 'openid',
				self::TICKET			=> 'ticket',
				self::NICKNAME			=> 'nickname',
				self::SEX				=> 'sex',
				self::CITY				=> 'city',
				self::PROVINCE			=> 'province',
				self::COUNTRY			=> 'country',
				self::SUBSCRIBE			=> 'subscribe',
				self::SUBSCRIBE_TIME	=> 'subscribeTime',
				self::UNSUBSCRIBE_TIME	=> 'unsubscribeTime',
				self::CREATE_TIME		=> 'createTime',
			),
			'columnTypes' => array(
				self::ID                => 'int',
				self::OPENID			=> 'int',
				self::TICKET            => 'string',
				self::NICKNAME			=> 'string',
				self::SEX				=> 'int',
				self::CITY				=> 'string',
				self::PROVINCE			=> 'string',
				self::COUNTRY			=> 'int',
				self::SUBSCRIBE			=> 'int',
				self::SUBSCRIBE_TIME	=> 'int',
				self::UNSUBSCRIBE_TIME	=> 'string',
				self::CREATE_TIME       => 'string',
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

		return $config['fracta_chevrolet_user'];
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