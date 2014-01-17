<?php
/**
 * @DESCRIPTION
 * 
 * 
 * @MODIFY
 * 13-11-19 上午9:59 create file
 * 
 * @author Kevin.Hu, Inc. <huwenhua@group-net.cn>
 * @version v2.1
 */

class XP_BModel_CallbackHandler
{
	public static function handler($originalId, $openid, $keyword)
	{
		$handle = System_Lib_App::app()->getConfig('callbackHandler');
		if(!array_key_exists($originalId, $handle) || !array_key_exists($keyword, $handle[$originalId])) return null;
		$handle = $handle[$originalId];
		$controller = $handle[$keyword][0];
		$action = $handle[$keyword][1];
		// 如果需要返回内容给微信，给出数组形式的内容，否则给出返回null
		$content = $controller::$action($openid, $originalId);
		return $content;
	}

	public static function commonHandler($originalId, $openid, $type, $content, $reply)
	{
		$handle = System_Lib_App::app()->getConfig('callbackCommonHandler');
		if(!array_key_exists($originalId, $handle)) return null;
		$handle = $handle[$originalId];
		$controller = $handle[0];
		$action = $handle[1];
		// 如果需要返回内容给微信，给出数组形式的内容，否则给出返回null
		$content = $controller::$action($originalId, $openid, $type, $content, $reply);
		return $content;
	}


}