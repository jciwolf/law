<?php
/**
 * Class XP_Lib_KeyManager
 * Define Cache Key
 */

class XP_Lib_KeyManager
{	

	//前端css和js版本号缓存
	public static function getVersionKey($key)
	{
		return 'XP-Version'.'-'.$key;
	}

	public static function GetWeixinAccessTokenKey()
	{
		return 'XP-WeixinAccessToken'.'-';
	}

	public static function GetWeixinAccessTokenLockKey()
	{
		return 'XP-WeixinAccessTokenLock'.'-';
	}
}