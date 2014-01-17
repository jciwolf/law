<?php
/**
 * @DESCRIPTION
 * 
 * 
 * @MODIFY
 * 13-11-13 下午5:10 create file
 * 
 * @author Kevin.Hu, Inc. <huwenhua@group-net.cn>
 * @version v2.1
 */

class XP_Lib_Partial
{
	public static function includes($partial, $params=array())
	{
		$partial_path = PROJECT_PATH . 'XP/View/Partial/' . $partial . '.php';
		$partial_params = array();
		foreach($params as $key=>$param)
		{
			$partial_params['P_'.$key] = $param;
		}
		extract($partial_params);
		include_once $partial_path;
	}
}