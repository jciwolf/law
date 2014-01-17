<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-6-24
 * Time: 下午6:44
 * To change this template use File | Settings | File Templates.
 */

class XP_Lib_Error {

	//const XP_EXAMPLE_ERRCODE = 200100;

	private static $errMapping = array(

		//self::XP_EXAMPLE_ERRCODE				=> 'Example Error description',

	);

	public static function getErrMsg($code)
	{
		if (isset(self::$errMapping[$code])) {
			return self::$errMapping[$code];
		}
		return $code;
	}

}