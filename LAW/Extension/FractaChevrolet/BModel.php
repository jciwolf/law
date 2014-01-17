<?php
/**
 * @DESCRIPTION
 * 
 * 
 * @MODIFY
 * 13-11-18 下午3:04 create file
 * 
 * @author Kevin.Hu, Inc. <huwenhua@group-net.cn>
 * @version v2.1
 */

class XP_Extension_FractaChevrolet_BModel
{
	public static $publicAccountId = 1;
	public static function getUserByOpenid($openid)
	{
		// get user info from db
		$dbo = XP_Extension_FractaChevrolet_UserModel::dataAccess();
		$dbo->filter(XP_Extension_FractaChevrolet_UserModel::OPENID, $openid);
		$user = $dbo->findOne();
		if(empty($user)) {
			$user = self::getUserFromWXApiByOpenid($openid);
			if(empty($user) || !is_array($user) || array_key_exists('errcode', $user)) return array();
			self::saveUserInfoToDB($user);
		}
		return $user;
	}

	public static function getUserFromWXApiByOpenid($openid)
	{
		try {
			$config = XP_Extension_FractaChevrolet_Config::getConfig();
			$publicAccountId = $config['publicAccountId'];
			$publicAccount = XP_BModel_PublicAccount::getPublicAccount($publicAccountId);
			$wxconfig = array('appId'=>$publicAccount->appId, 'appSecret'=>$publicAccount->appSecret, 'token'=>$publicAccount->token, 'publicAccountId'=>$publicAccountId);
			$user = XP_Lib_Weixin::portal($wxconfig)->getUserInfo($openid);
		} catch (Exception $e) {
			System_Lib_App::app()->recordRunTime('checkUser end error:' . $e->getMessage());
			return null;
		}
		return $user;
	}

	public static function saveUserInfoToDB($user)
	{
		$fuser = new XP_Extension_FractaChevrolet_UserModel();
		$fuser->openid = $user['openid'];
        $fuser->nickname = $user['nickname'];
		$fuser->sex = $user['sex'];
		$fuser->city = $user['city'];
		$fuser->province = $user['province'];
		$fuser->country = $user['country'];
		$fuser->subscribe = $user['subscribe'];
		$fuser->subscribeTime = date('Y-m-d H:i:s', $user['subscribe_time']);
		$fuser->createTime = date('Y-m-d H:i:s');
		$fuser->save();
	}
}