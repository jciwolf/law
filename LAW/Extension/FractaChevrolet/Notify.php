<?php
/**
 * @DESCRIPTION
 * 
 * 
 * @MODIFY
 * 13-11-15 下午4:04 create file
 * 
 * @author Kevin.Hu, Inc. <huwenhua@group-net.cn>
 * @version v2.1
 */

class XP_Extension_FractaChevrolet_Notify extends System_Lib_Controller
{
	// errcode
	const ERR_API_LOST			= -10000;
	const ERR_PARAMS_SIGN		= -10001;
	const ERR_PARAMS_LOST		= -10003;
	const ERR_PARAMS_TIMEOUT	= -10004;
	const ERR_PARAMS_VER		= -10005;
    const ERR_PARAMS_OPENID     = -10006;

	const MD5KEY		= 'M*)y!Qu0Tm-3';
	const VER_1_0		= 1.0;

	const NOTIFY_TYPE_BIND			= 1;
	const NOTIFY_TYPE_RESERVE		= 2;
	const NOTIFY_TYPE_COMMON_MSG	= 3;
	const NOTIFY_TYPE_POINT_DEAL	= 4;
	const NOTIFY_TYPE_POINT_OUTTIME	= 5;
	const NOTIFY_TYPE_UPGRADE		= 6;
	const NOTIFY_TYPE_KEEPGRADE		= 7;
	const NOTIFY_TYPE_DOWNGRADE		= 8;
	const NOTIFY_TYPE_BIRTHDAY		= 9;
    const NOTIFY_TYPE_BOOKIT        = 10;

	const BIND_STATUS_NO		= 0;    // 未绑定，未激活
	const BIND_STATUS_BINDED   	= 1;    // 已绑定，未激活
	const BIND_STATUS_ACTIVE	= 2;    // 未绑定，已激活
    const BIND_STATUS_SUCCESS	= 3;    // 已绑定，已激活

	const RESERVE_STATUS_CANCEL	= 0;
	const RESERVE_STATUS_OK		= 1;
	const RESERVE_STATUS_COMPLETE	= 2;

    const REPLY_REMARK  = '如有疑问，请随时咨询小U，或拨打我们的客服热线400-111-1911。';

	public $logid = null;

	public function callbackAction()
	{
		$cdata = System_Lib_App::getPost('cdata', System_Lib_Request::TYPE_STRING, null);
		if(is_null($cdata))
			$cdata = System_Lib_App::get('cdata', System_Lib_Request::TYPE_STRING, null);
		// add log
		$this->logid = ''. time() . rand(100, 999);
		XP_Lib_Utility::log('Fracta Request ['.$this->logid.']: cdata='.$cdata, 'FractaRequest');
		if(is_null($cdata))
			return $this->_retError(self::ERR_PARAMS_LOST); //
		$arrData = json_decode($cdata, true);
		if(!$this->isSignCorrect($arrData))
			return $this->_retError(self::ERR_PARAMS_SIGN);
		if(array_key_exists('tm', $arrData) && $this->isTimeout($arrData['tm']))
			return $this->_retError(self::ERR_PARAMS_TIMEOUT);
		if(array_key_exists('ver', $arrData) && !$this->isVerCorrect($arrData['ver']))
			return $this->_retError(self::ERR_PARAMS_VER);
		if(!array_key_exists('type', $arrData) || !array_key_exists('openid', $arrData))
			return $this->_retError(self::ERR_PARAMS_LOST);
        if(strlen($arrData['openid']) < 28)
            return $this->_retError(self::ERR_PARAMS_OPENID);

		// switch type = 1, 2, 3 default
		switch ($arrData['type'])
		{
			case self::NOTIFY_TYPE_BIND:
				$retCode = $this->userBindStatus($arrData);
				break;
			case self::NOTIFY_TYPE_RESERVE:
				$retCode = $this->maintenanceReservation($arrData);
				break;
            case self::NOTIFY_TYPE_COMMON_MSG:
                $retCode = $this->commonMsg($arrData);
                break;
			case self::NOTIFY_TYPE_POINT_DEAL:
				$retCode = $this->pointDeal($arrData);
				break;
			case self::NOTIFY_TYPE_POINT_OUTTIME:
				$retCode = $this->pointOuttime($arrData);
				break;
			case self::NOTIFY_TYPE_UPGRADE:
				$retCode = $this->upgrade($arrData);
				break;
			case self::NOTIFY_TYPE_KEEPGRADE:
				$retCode = $this->keepGrade($arrData);
				break;
			case self::NOTIFY_TYPE_DOWNGRADE:
				$retCode = $this->downgrade($arrData);
				break;
			case self::NOTIFY_TYPE_BIRTHDAY:
				$retCode = $this->birthday($arrData);
				break;
            case self::NOTIFY_TYPE_BOOKIT:
                $retCode = $this->bookit($arrData);
                break;
			default:
				return $this->_retError(self::ERR_PARAMS_LOST);
		}
		if ($retCode == 0)
			return $this->_retMessage(array());
		else
			return $this->_retError($retCode);
	}

	// 用户绑定
	private function userBindStatus($data)
	{
		// status
		if (!array_key_exists('status', $data)) return self::ERR_PARAMS_LOST;
		$content = '';
		$retCode = 0;
		switch($data['status'])
		{
			case self::BIND_STATUS_NO:
				$content = '小U提醒您，您还没有绑定身份哟！请尽快进行绑定，体验UCLUB会员尊享服务!'. "\n" .
                    '<a href="http://wx.fractalist.com.cn/ChevroletWX/h5/UserBind.aspx?openid='. $data['openid'] .'">绑定/激活</a>';
				break;
			case self::BIND_STATUS_BINDED:
				$content = '小U恭喜您，您的账户已经成功绑定，'.
                    '赶紧激活积分账号，享受更全面的会员积分增值服务吧！'. "\n".
                    '<a href="http://uclub.mysgm.com.cn/mobile/activepre/active_pre.html?openid='.$data['openid'].'">激活积分积分账户</a>';
				break;
			case self::BIND_STATUS_SUCCESS:
                $content = '小U恭喜您，您的帐号已经成功绑定并激活，您可以享受更全面的会员积分增值服务。'.
                    '赶紧前往雪佛兰UCLUB官方微信的底部菜单“账户-幸运摩天轮”，参加每周一次的好礼大放送吧！'."\n".
                    '<a href="http://wx.fractalist.com.cn/ChevroletWX/h5/Scratch.aspx?openid='.$data['openid'].'">幸运摩天轮，周周赢好礼！</a>';
				break;
			default:
				return self::ERR_PARAMS_LOST;
		}
        try{
            $this->sendCustomMessage($content,$data['openid']);
        }catch (Exception $e){
            $retCode = $e->getCode();
            XP_Lib_Utility::log('Send Custom Message Failed By UserBindStatus: '.json_encode($data).'; Error Code: '.
            $retCode . '; Error Message: ' . $e->getMessage(), 'SendWeixinMessage');
        }
		// @TODO send messages
		return $retCode;
	}

    // 通用消息接口
    private function commonMsg($data)
    {
        // status
        if (!array_key_exists('content', $data)) return self::ERR_PARAMS_LOST;
        $retCode = 0;
        $content = stripslashes($data['content']);
        // @TODO send messages
        try{
            $this->sendCustomMessage($content,$data['openid']);
        }catch (Exception $e){
            return $e->getCode();
            XP_Lib_Utility::log('Send Custom Message Failed By CommonMsg: '.json_encode($data).'; Error Code: '.
                $retCode . '; Error Message: ' . $e->getMessage(), 'SendWeixinMessage');
        }
        return $retCode;
    }

	// 维修维护预约
    private function maintenanceReservation($data)
    {
        if (!array_key_exists('status', $data)) return self::ERR_PARAMS_LOST;
        $templateId = $this->getTemplateId('maintenanceReservation');
        $body = array();
        $status = '';
        $remark = '感谢您对雪佛兰UCLUB车主俱乐部的支持。' . self::REPLY_REMARK;

        switch($data['status'])
        {
            case self::RESERVE_STATUS_CANCEL:
                $status = '已取消';
                $title  = '您好，尊敬的UCLUB会员，您预约的维保服务已取消。如需服务请您重新预约。';
                break;
            case self::RESERVE_STATUS_COMPLETE:
                $status = '已完成';
                $title  = '您好，尊敬的UCLUB会员，您预约的 维保 服务已完成';
                break;
            case self::RESERVE_STATUS_OK:
                $status = '已预约';
                $title  = '您好，尊敬的UCLUB会员，您预约的维保服务已生效。请您准时到店，我们将恭候您的光临。';
                break;
            default:
                return self::ERR_PARAMS_LOST;
        }
        $fwtype = $data['fwtype'] == 0 ? '维修' : '保养';

        $body['title']['value'] = $title;
        $body['title']['color'] = '#000000';
        $body['productType']['value'] = '服务';
        $body['productType']['color'] = '#000000';
        $body['name']['value'] = $fwtype;
        $body['name']['color'] = '#000000';
        $body['time']['value'] = $data['reservetime'];
        $body['time']['color'] = '#000000';
        $body['result']['value'] = $status;
        $body['result']['color'] = '#000000';
        $body['remark']['value'] = $remark;
        $body['remark']['color'] = '#000000';

        $content = array('touser'=>$data['openid'], 'template_id'=>$templateId, 'data'=>$body, 'url'=>'http://wx.fractalist.com.cn/ChevroletWX/h5/DealerSearch.aspx', "topcolor"=>"#000000");
        $retCode = 0;
        try{
            $this->sendTemplateMessage($content);
        }catch (Exception $e){
            $retCode = $e->getCode();
            XP_Lib_Utility::log('Send Template Message Failed By MaintenanceReservation: '.json_decode($data).
                '; Error Code: '. $retCode . '; Error Message: ' . $e->getMessage(), 'SendWeixinMessage');
        }
        return $retCode;

    }

    // 预约 模板消息 测试
    private function bookit($data)
    {
        $templateId = $this->getTemplateId('bookit');
        $body = array();
        $body['title']['value']      = "预约成功";
        $body['carType']['value']    = "BMW X5";
        $body['name']['value']       = "Kevin.Hu";
        $body['tel']['value']        = "13366204032";
        $body['expDate']['value']    = "2013年12月03日";
        $body['remark']['value']     = "请您在有效期内，尽快前往高碑店四通店取车。";
        $content = array('touser'=>$data['openid'], 'template_id'=>$templateId, 'data'=>$body);
        $retCode = 0;
        try{
            $this->sendTemplateMessage($content);
        }catch (Exception $e){
            $retCode = $e->getCode();
            XP_Lib_Utility::log('Send Template Message Failed By bookit: '.json_decode($data).
            '; Error Code: '. $retCode . '; Error Message: ' . $e->getMessage(), 'SendWeixinMessage');
        }
        return $retCode;
    }


	// 积分交易
	private function pointDeal($data)
	{
		if (!array_key_exists('card', $data)) return self::ERR_PARAMS_LOST;
		$actype = $data['actype'] == 1 ? '获得' : '未知';
		$actype = $data['actype'] == 2 ? '消费' : $actype;
		$templateId = $this->getTemplateId('pointDeal');
		$body = array();

		$body['title']['value'] = '尊敬的UCLUB会员:\n您尾号'.$data['card'].'的雪佛兰UCLUB会员卡最近交易信息如下：';
        $body['title']['color'] = "#000000";
        $body['type']['value'] = $actype;
        $body['type']['color'] = "#000000";
        $body['integral']['value'] = $data['points'];
        $body['integral']['color'] = "#000000";
        $body['all']['value'] = $data['userpoints'];
        $body['all']['color'] = "#000000";
        $body['remark']['value'] = '详细的积分规则请查看会员须知。' . self::REPLY_REMARK;
        $body['remark']['color'] = "#000000";

		$content = array('touser'=>$data['openid'], 'template_id'=>$templateId, 'data'=>$body, 'url'=>'http://uclub.mysgm.com.cn/mobile/pointpre/point_pre.html', "topcolor"=>"#000000");
        $retCode = 0;
        try{
            $this->sendTemplateMessage($content);
        }catch (Exception $e){
            $retCode = $e->getCode();
            XP_Lib_Utility::log('Send Template Message Failed By PointDeal: '.json_decode($data).
            '; Error Code: '. $retCode . '; Error Message: ' . $e->getMessage(), 'SendWeixinMessage');
        }
		return $retCode;
	}

	// 积分到期
	private function pointOuttime($data)
	{
		if (!array_key_exists('points', $data)) return self::ERR_PARAMS_LOST;
		$templateId = $this->getTemplateId('pointOutTime');
		$body = array();
		$body['title']['value'] = '尊敬的会员，您好：';
        $body['title']['color'] = "#000000";
		$body['Card']['All']['value'] = $data['points'];
        $body['Card']['All']['color'] = "#000000";
        $body['Card']['Expire']['value'] = sprintf('%s年%s月%s日', $data['year'], $data['month'], $data['day']);
        $body['Card']['Expire']['color'] = "#000000";
		$body['remark']['value'] = '\n请尽快使用，不要浪费了哟！' . self::REPLY_REMARK;
        $body['remark']['color'] = "#000000";
		$content = array('touser'=>$data['openid'], 'template_id'=>$templateId, 'data'=>$body, 'url'=>'http://uclub.mysgm.com.cn/mobile/pointpre/point_pre.html', "topcolor"=>"#000000");
        $retCode = 0;
        try{
            $this->sendTemplateMessage($content);
        }catch (Exception $e){
            $retCode = $e->getCode();
            XP_Lib_Utility::log('Send Template Message Failed By PointOuttime: '.json_decode($data).
            '; Error Code: '. $retCode . '; Error Message: ' . $e->getMessage(), 'SendWeixinMessage');
        }
		return $retCode;
	}

	// 会员升级
	private function upgrade($data)
	{
		if (!array_key_exists('cardtype', $data)) return self::ERR_PARAMS_LOST;
		$cardType = $data['cardtype'] == 1 ? '金卡' : '未知';
		$cardType = $data['cardtype'] == 2 ? '银卡' : $cardType;
		$tips = $data['cardtype'] == 2 ? '您只需要在一年内进站6次，并消费满6000元便可升级为金卡会员。' : '';

		$templateId = $this->getTemplateId('memberUpgrade');
		$body['title']['value'] = '您好，恭喜您成功升级为雪佛兰UCLUB'.$cardType.'会员，尊享'.$cardType.'会员贴心服务。';
        $body['title']['color'] = "#000000";
		$body['styles']['value'] = $cardType . '会员';
        $body['styles']['color'] = "#000000";
        $body['period']['value'] = $data['year'] . '年'.$data['month'].'月'.$data['day'] . '日';
        $body['period']['color'] = "#000000";
		$body['Tips'] = $tips;
        $body['Tips']['color'] = "#000000";
		$body['remark']['value'] = '\n'.$tips . self::REPLY_REMARK;
        $body['remark']['color'] = "#000000";
		$content = array('touser'=>$data['openid'], 'template_id'=>$templateId, 'data'=>$body, 'url'=>'http://wx.fractalist.com.cn/ChevroletWX/h5/MemberNotice.aspx', "topcolor"=>"#000000");
        $retCode = 0;
        try{
            $this->sendTemplateMessage($content);
        }catch (Exception $e){
            $retCode = $e->getCode();
            XP_Lib_Utility::log('Send Template Message Failed By Upgrade: '.json_decode($data).
            '; Error Code: '. $retCode . '; Error Message: ' . $e->getMessage(), 'SendWeixinMessage');
        }
		return $retCode;
	}

	// 会员保级
	private function keepGrade($data)
	{
		if (!array_key_exists('cardtype', $data)) return self::ERR_PARAMS_LOST;
		$cardType = $data['cardtype'] == 1 ? '金卡' : '未知';
		$cardType = $data['cardtype'] == 2 ? '银卡' : $cardType;
        $tips = '\n您只需要在一年内进站'.$data['times'].'次，并消费满'.$data['fee'].'元即可完成保级，继续尊享UCLUB'.$cardType.'会员贴心服务。';

		$templateId = $this->getTemplateId('memberKeepGrade');
        $body['title']['value'] = '小U提醒您：';
        $body['title']['color'] = "#000000";
        $body['name']['value'] = 'UCLUB '.$cardType.'会员服务';
        $body['name']['color'] = "#000000";
        $body['expDate']['value'] = sprintf('%s年%s月%s日', $data['year'], $data['month'], $data['day']);
        $body['expDate']['color'] = "#000000";
        $body['remark']['value'] = $tips . self::REPLY_REMARK;
        $body['remark']['color'] = "#000000";

		$content = array('touser'=>$data['openid'], 'template_id'=>$templateId, 'data'=>$body, 'url'=>'http://wx.fractalist.com.cn/ChevroletWX/h5/MemberNotice.aspx', "topcolor"=>"#000000");
        $retCode = 0;
        try{
            $this->sendTemplateMessage($content);
        }catch (Exception $e){
            $retCode = $e->getCode();
            XP_Lib_Utility::log('Send Template Message Failed By KeepGrade: '.json_decode($data).
            '; Error Code: '. $retCode . '; Error Message: ' . $e->getMessage(), 'SendWeixinMessage');
        }
		return $retCode;
	}

	// 会员降级
	private function downgrade($data)
	{
		if (!array_key_exists('cardtype', $data)) return self::ERR_PARAMS_LOST;
		$cardType = $data['cardtype'] == 1 ? '金卡' : '未知';
		$cardType = $data['cardtype'] == 2 ? '银卡' : $cardType;
        $tips = '\n您只需要在一年内进站'. $data['times'] .'次，并消费满'. $data['fee'] .'元，便可重新升级为'.$cardType.'会员，尊享贴心服务。';

		$templateId = $this->getTemplateId('memberDowngrade');
        $body['title']['value'] = '小U提醒您：';
        $body['title']['color'] = "#000000";
        $body['name']['value'] = 'UCLUB '.$cardType.'会员服务';
        $body['name']['color'] = "#000000";
        $body['expDate']['value'] = sprintf('%s年%s月%s日', $data['year'], $data['month'], $data['day']);
        $body['expDate']['color'] = "#000000";
        $body['remark']['value'] = $tips . self::REPLY_REMARK;
        $body['remark']['color'] = "#000000";
		$content = array('touser'=>$data['openid'], 'template_id'=>$templateId, 'data'=>$body, 'url'=>'http://wx.fractalist.com.cn/ChevroletWX/h5/MemberNotice.aspx', "topcolor"=>"#000000");
        $retCode = 0;
        try{
            $this->sendTemplateMessage($content);
        }catch (Exception $e){
            $retCode = $e->getCode();
            XP_Lib_Utility::log('Send Template Message Failed By DownGrade: '.json_decode($data).
            '; Error Code: '. $retCode . '; Error Message: ' . $e->getMessage(), 'SendWeixinMessage');
        }
		return $retCode;
	}

	// 生日
	private function birthday($data)
	{
		if (!array_key_exists('age', $data)) return self::ERR_PARAMS_LOST;
		$templateId = $this->getTemplateId('memberBirthday');
		$body['title']['value'] = '用户生日提醒';
        $body['remark']['color'] = "#000000";
		$body['Des']['Card'] = $data['card'];
        $body['remark']['color'] = "#000000";
		$body['Des']['Callback'] = "阅读全文(点开为更多积分获取的方式介绍)";
        $body['remark']['color'] = "#000000";
		$content = array('touser'=>$data['openid'], 'template_id'=>$templateId, 'data'=>$body, 'url'=>'http://www.chevrolet.com.cn/brandsite/', "topcolor"=>"#000000");
        $retCode = 0;
        try{
            $this->sendTemplateMessage($content);
        }catch (Exception $e){
            $retCode = $e->getCode();
            XP_Lib_Utility::log('Send Template Message Failed By Birthday: '.json_decode($data).
            '; Error Code: '. $retCode . '; Error Message: ' . $e->getMessage(), 'SendWeixinMessage');
        }
		return $retCode;
	}

    public function accesstokenAction()
    {
        $cdata = System_Lib_App::getPost('cdata', System_Lib_Request::TYPE_STRING, null);
        if(is_null($cdata))
            $cdata = System_Lib_App::get('cdata', System_Lib_Request::TYPE_STRING, null);
        // add log
        $this->logid = ''. time() . rand(100, 999);
        XP_Lib_Utility::log('Fracta Request ['.$this->logid.']: cdata='.$cdata, 'FractaRequest');
        if(is_null($cdata))
            return $this->_retError(self::ERR_PARAMS_LOST); //
        $arrData = json_decode($cdata, true);
        if(!$this->isSignCorrect($arrData))
            return $this->_retError(self::ERR_PARAMS_SIGN);
        if(array_key_exists('tm', $arrData) && $this->isTimeout($arrData['tm']))
            return $this->_retError(self::ERR_PARAMS_TIMEOUT);
        if(array_key_exists('ver', $arrData) && !$this->isVerCorrect($arrData['ver']))
            return $this->_retError(self::ERR_PARAMS_VER);

        $config =  XP_Extension_FractaChevrolet_Config::getConfig();
        $publicAccountId = $config['publicAccountId'];
        $publicAccount = XP_BModel_PublicAccount::getPublicAccount($publicAccountId);
        $wxconfig = array('appId'=>$publicAccount->appId, 'appSecret'=>$publicAccount->appSecret, 'token'=>$publicAccount->token, 'publicAccountId'=>$publicAccountId);
        $acctoken = XP_Lib_Weixin::portal($wxconfig)->getAccessToken($publicAccountId, $publicAccount->appId, $publicAccount->appSecret);

        $arrAcctoken = array('access_token'=> $acctoken);
        XP_Lib_Utility::log('Fracta Response ['.$this->logid.']: res='.json_encode($arrAcctoken), 'FractaRequest');
        return $this->_retMessage($arrAcctoken);
    }


	private function sendCustomMessage($content, $openId)
	{
		// @TODO send messages
		$msg = array(
			'touser' => $openId,
			'msgtype' => 'text',
			'text' => array(
				"content"=>$content,
			)
		);
		XP_Lib_Utility::log('Fracta Request Send Custom Message ['.$this->logid.']: [Start] msg='.json_encode($msg), 'FractaRequest');
		$config =  XP_Extension_FractaChevrolet_Config::getConfig();
		$publicAccountId = $config['publicAccountId'];
		$publicAccount = XP_BModel_PublicAccount::getPublicAccount($publicAccountId);
		$wxconfig = array('appId'=>$publicAccount->appId, 'appSecret'=>$publicAccount->appSecret, 'token'=>$publicAccount->token, 'publicAccountId'=>$publicAccountId);
		if( XP_Lib_Weixin::portal($wxconfig)->sendCustomMsg($msg)){
			XP_Lib_Utility::log('Fracta Request Send Custom Message ['.$this->logid.']: [Success] msg='.json_encode($msg), 'FractaRequest');
		}else{
			XP_Lib_Utility::log('Fracta Request Send Custom Message ['.$this->logid.']: [Failed] msg='.json_encode($msg), 'FractaRequest');
		}
	}

	private function sendTemplateMessage($content)
	{
		$config =  XP_Extension_FractaChevrolet_Config::getConfig();
		$publicAccountId = $config['publicAccountId'];
		$publicAccount = XP_BModel_PublicAccount::getPublicAccount($publicAccountId);
		$wxconfig = array('appId'=>$publicAccount->appId, 'appSecret'=>$publicAccount->appSecret, 'token'=>$publicAccount->token, 'publicAccountId'=>$publicAccountId);
		XP_Lib_Utility::log('Fracta Request Send Template Message ['.$this->logid.']: [Start] content='.json_encode($content), 'FractaRequest');
		if( XP_Lib_Weixin::portal($wxconfig)->sendTemplateMsg($content))
		{
			XP_Lib_Utility::log('Fracta Request Send Template Message ['.$this->logid.']: [Success] content='.json_encode($content), 'FractaRequest');
		}else{
			XP_Lib_Utility::log('Fracta Request Send Template Message ['.$this->logid.']: [Failed] content='.json_encode($content), 'FractaRequest');
		}
	}

	private function isSignCorrect($data)
	{
		$isSigned = false;
		if(!array_key_exists('sign', $data)) return $isSigned;
		$sign = $data['sign'];
		unset($data['sign']);
		$data['md5key'] = self::MD5KEY;
		ksort($data);
		$strParam = join('', $data);
		$_sign = md5($strParam);
		if ($sign == $_sign) $isSigned = true;
		return $isSigned;
	}

	private function isTimeout($time)
	{
		$isTimeout = true;
		if($time + 3600 >= time())
		{
			$isTimeout = false;
		}
		return $isTimeout;
	}

	private function isVerCorrect($ver)
	{
		$isVer = false;
		if ($ver == self::VER_1_0)
		{
			$isVer = true;
		}
		return $isVer;
	}

	private function getConfig($name)
	{
		$config = XP_Extension_FractaChevrolet_Config::getConfig();
		if(array_key_exists($name, $config))
		{
			return $config[$name];
		}
		return null;
	}

	private function getTemplateId($name)
	{
		$templateIds = $this->getConfig('templateId');
		if(array_key_exists($name, $templateIds))
		{
			return $templateIds[$name];
		}
		return null;
	}

	private function _retError($code, $errmsg = null)
	{
		$ret = array();
		$ret['retData'] = array();
		$ret['retCode'] = $code;
		$jret = json_encode($ret);
		XP_Lib_Utility::log('Fracta Request Error [' . $this->logid . ']:  return ' . $jret, 'FractaRequest');
		$this->assignData('retData', $jret);
		$this->render('Ajax');
	}

	private function _retMessage($data)
	{
		$ret = array();
		$ret['retData'] = $data;
		$ret['retCode'] = 0;
		$jret = json_encode($ret);
		XP_Lib_Utility::log('Fracta Request Successful End [' . $this->logid . ']: return ' . $jret, 'FractaRequest');
		$this->assignData('retData', $jret);
		$this->render('Ajax');
	}
}