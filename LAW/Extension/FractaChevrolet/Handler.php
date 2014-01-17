<?php
/**
 * @DESCRIPTION
 * 
 * 
 * @MODIFY
 * 13-11-18 下午6:56 create file
 * 
 * @author Kevin.Hu, Inc. <huwenhua@group-net.cn>
 * @version v2.1
 */

class XP_Extension_FractaChevrolet_Handler
{
	private static $logid = null;
	/**
	 * @param $openid
	 * only send request 异步
	 */
	public static function queryUserPoints($openid, $originalId)
	{
		if(is_null(self::$logid)) self::$logid = ''. time() . rand(100, 999);
		$status = self::userBindStatus($openid,$originalId);
		if(!is_null($status)){
			return $status;
		}
		$url = 'http://wx.fractalist.com.cn/ChevroletWX/InterFace/GetPoints.aspx';
		$config = XP_Extension_FractaChevrolet_Config::getConfig();
		$ver = $config['ver10'];
		$params = array('openid'=>$openid, 'ver'=>$ver, 'tm'=>time());
		$params['sign'] = self::getParamsSign($params);
		$params = array('cdata'=>json_encode($params));
		XP_Lib_Utility::log('Gaopeng To Fracta Request ['.self::$logid.']: api=queryUserPoints, params=' . json_encode($params), 'FractaRequest');
		$rs = self::httpSend($url, $params, 2);
		XP_Lib_Utility::log('Gaopeng To Fracta Response ['.self::$logid.']: api=queryUserPoints, ret=' . json_encode($rs), 'FractaRequest');
        $rs = json_decode($rs, true);
		if($rs['retCode'] == 0)
		{
			return $rs['retData']['content'];
		}
		return null;
	}

	/**
	 * @param $openid
	 * @param $originalId
	 * @return null|string
	 *
	 * 用户点击绑定按钮时 触发判断是否绑定
	 */
	public static function bindUser($openid, $originalId)
	{
		if(is_null(self::$logid)) self::$logid = ''. time() . rand(100, 999);
		XP_Lib_Utility::log('Gaopeng To Fracta Request ['.self::$logid.']: api=bindUser, openid=' . $openid, 'FractaRequest');
		$status = self::userBindStatus($openid,$originalId);
		if(is_null($status)){
            $content = '小U恭喜您，您的帐号已经成功绑定并激活，您可以享受更全面的会员积分增值服务。'.
                '赶紧前往雪佛兰UCLUB官方微信的底部菜单“账户-幸运摩天轮”，参加每周一次的好礼大放送吧！'."\n".
                '<a href="http://wx.fractalist.com.cn/ChevroletWX/h5/Scratch.aspx?openid='.$openid.'">幸运摩天轮，周周赢好礼！</a>';
            XP_Lib_Utility::log('Gaopeng To Fracta Request ['.self::$logid.']: api=bindUser, content=' . $content, 'FractaRequest');
        }else{
            $content = $status;
        }
		return $content;
	}

	/**
	 * @param $openid
	 * query openid 如果绑定状态为未绑定，那么返回提醒绑定的消息
	 */
	public static function userBindStatus($openid, $originalId)
	{
		if(is_null(self::$logid)) self::$logid = ''. time() . rand(100, 999);
		$url = 'http://wx.fractalist.com.cn/ChevroletWX/InterFace/GetBindStatus.aspx';
		$config = XP_Extension_FractaChevrolet_Config::getConfig();
		$ver = $config['ver10'];

		$params = array('openid'=>$openid, 'ver'=>$ver, 'tm'=>time());
		$params['sign'] = self::getParamsSign($params);
		$params = array('cdata'=>json_encode($params));
		XP_Lib_Utility::log('Gaopeng To Fracta Request ['.self::$logid.']: api=userBindStatus, url='.$url.', params=' . json_encode($params), 'FractaRequest');
        try{
            $ret = self::httpSend($url, $params, 3);
            $ret = json_decode($ret, true);
            XP_Lib_Utility::log('Gaopeng To Fracta Response ['.self::$logid.']: api=userBindStatus, ret=' . json_encode($ret), 'FractaRequest');
        }catch (Exception $e)
        {
            XP_Lib_Utility::log('Gaopeng To Fracta Request['.self::$logid.'] Exception: api=userBindStatus, params=' . json_encode($params), 'FractaRequest');
            exit;
        }

		if(!is_null($ret) && intval($ret['retCode']) == 3)
		{
			return null;
        }else if(!is_null($ret) && intval($ret['retCode']) == 1){
            $content = '小U恭喜您，您的账户已经成功绑定，'.
                '赶紧激活积分账号，享受更全面的会员积分增值服务吧！'. "\n".
                '<a href="http://uclub.mysgm.com.cn/mobile/activepre/active_pre.html?openid='.$openid.'">激活积分积分账户</a>';
            return $content;
		}else{
            $userinfo = XP_Extension_FractaChevrolet_BModel::getUserByOpenid($openid);
			$content = '小U提醒您，您还没有绑定身份哟！请尽快进行绑定，体验UCLUB会员尊享微服务！'. "\n".
                '<a href="http://wx.fractalist.com.cn/ChevroletWX/h5/UserBind.aspx?openid='. $openid .'&sex='. $userinfo->sex .'">绑定/激活</a>';
			XP_Lib_Utility::log('Gaopeng To Fracta Response ['.self::$logid.']: api=userBindStatus, content=' . $content , 'FractaRequest');
			return $content;
		}
	}

    public static function maintenanceReservation($openid, $orignalId)
    {
        // check bind status
        if(is_null(self::$logid)) self::$logid = ''. time() . rand(100, 999);
        XP_Lib_Utility::log('Weixin Menu callback ['.self::$logid.']: menu=maintenanceReservation, openid=' . $openid, 'WeinxinCallback');
        $status = self::userBindStatus($openid, $orignalId);
        $content = '';
        if(is_null($status))
        {
            $userinfo = XP_Extension_FractaChevrolet_BModel::getUserByOpenid($openid);
            $content = '雪佛兰UCLUB会员只需要提前24小时进行维修保养微信预约，成功后，即可尊享维保VIP绿色通道优先安排！'. "\n" .
                '<a href="http://wx.fractalist.com.cn/ChevroletWX/h5/Reservation.aspx?openid='.$openid.'&sex='.$userinfo->sex.'">​点击进入</a>';
        }else{
            $content = $status;
        }

        return $content;

    }

	/**
	 * @param $arrLogs
	 * report log
	 */
	public static function reportLog($originalId, $openid, $type, $content='', $reply='')
	{
		if(is_null(self::$logid)) self::$logid = ''. time() . rand(100, 999);
		XP_Lib_Utility::log('Gaopeng To Fracta Request ['.self::$logid.']: api=reportLog , Start.', 'FractaRequest');
		$url = 'http://wx.fractalist.com.cn/ChevroletWX/InterFace/GetReportLog.aspx';
		$config = XP_Extension_FractaChevrolet_Config::getConfig();
		$ver = $config['ver10'];
		// 根据openid 获得用户基本信息
		$userinfo = XP_Extension_FractaChevrolet_BModel::getUserByOpenid($openid); // @TODO add this function
		$arrContent = json_decode($content, true);
		$msgType = XP_BModel_Callback::$messageType;
		$mtype = array_key_exists($type, $msgType) ? $msgType[$type] : $msgType['unknow'];

		if ($arrContent) {
			if(array_key_exists('event', $arrContent)) $content = $arrContent['event'];
			if(array_key_exists('event', $arrContent) && $arrContent['event'] == 'click')
			{
				$mtype = '99';
				$content = $arrContent['eventKey'];
			}
			if(array_key_exists('event', $arrContent) && $arrContent['event'] == 'location')
			{
				$mtype = $msgType['location'];
				$content = $arrContent['latitude'] .','.$arrContent['longitude'] .','.$arrContent['precision'];
			}
			if(array_key_exists('text', $arrContent)){
				$content = $arrContent['text'];
			}
		}
		$params = array(
			'openid'	=> $openid,
			'nickname'	=> isset($userinfo->nickname) ? $userinfo->nickname : '',
			'gender'	=> isset($userinfo->sex) ? $userinfo->sex : '',
			'city'		=> isset($userinfo->city) ? $userinfo->city : '',
			'type'		=> $mtype,
			'content'	=> $content,
			'reply'		=> $reply,
			'createTime'=> date('Y-m-d H:i:s'),
			'ver'		=> $ver,
			'tm'		=> time(),
		);
		if(!is_null($reply)) $params['reply'] = $reply;
		$params['sign'] = self::getParamsSign($params);
		$params = array('cdata'=>json_encode($params));
		XP_Lib_Utility::log('Gaopeng To Fracta Request ['.self::$logid.']: api=reportLog, url='.$url.', params=' . json_encode($params), 'FractaRequest');
        try{
            $rs = self::httpSend($url, $params, 1);
        }catch(Exception $e)
        {
            XP_Lib_Utility::log('Gaopeng To Fracta Request['.self::$logid.'] Exception: api=reportLog, params=' . json_encode($params), 'FractaRequest');
        }
        $rs = json_decode($rs, true);
        XP_Lib_Utility::log('Gaopeng To Fracta Response ['.self::$logid.']: api=reportLog, retCode=' . $rs['retCode'] . '; content: ' . $rs['retData']['Content'] . '; mytype: ' . $mtype, 'FractaRequest');
		if($mtype == 1 && $rs['retCode'] ==0) {
			return $rs['retData']['content'];
		}
		return null;
	}


	private static function httpSend($url, $params, $timeout=3)
	{
		if (isset($params) && is_array($params)) {
			$params = http_build_query($params); //ADD
		}
		$resp = '';
		$mapiStartTime = microtime(true);
		$repeat = 3;
		$count = 1;
		System_Lib_App::app()->recordRunTime('mapi before url=' . $url . '?' . $params);
		while ($count <= $repeat) {
			$return = System_Lib_ServerAccessor::CallCURLPOST($url, $params, $resp, array(), $timeout); //ADD
			switch ($return) {
				case  0:
					$result = 'ok';
					break;
				case -1:
					$result = 'contect error';
					break;
				case -2:
					$result = 'responseCode not 200';
					break;
			}
			if ($return === 0) {
				System_Lib_App::app()->recordRunTime('mapi end count=' . $count);
                $resp = stripslashes($resp);
				$resp = json_encode($resp);
				//返回的数据反编码
				if (empty($resp) || ($respJson = json_decode($resp, true)) === false) {
					return false;
				}
				return $respJson;
			} else {
				// log
			}
			$count++;
			usleep(500000);
		}
		System_Lib_App::app()->recordRunTime('mapi end count=' . $count);
		return false;
	}

	private static function getParamsSign($data)
	{
		$config = XP_Extension_FractaChevrolet_Config::getConfig();
		$data['md5key'] = $config['md5key'];
		ksort($data);
		$strParam = join('', $data);
		$_sign = md5($strParam);
		return $_sign;
	}
}