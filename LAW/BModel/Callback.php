<?php

class XP_BModel_Callback {

	/**  '消息类型 1:text 2:image 3:location 4:link 5:event 6:music 7:news 8:voice 9:scan' */
	public static $messageType = array(
		'unknow'	=> 0,
		'text'		=> 1,
		'image'		=> 2,
		'location'	=> 3,
		'link'		=> 4,
		'event'		=> 5,
		'music'		=> 6,
		'news'		=> 7,
		'voice'		=> 8,
		'scan'		=> 9,
	);

	public static function receive($msg)
	{
		if (empty($msg)) return '';
		$postObj      = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
		$fromUsername = (string)$postObj->FromUserName;
		$toUsername   = (string)$postObj->ToUserName;
		$msgType      = (string)$postObj->MsgType;
		$logContent   = null;
        $publicAccountStatus=XP_BModel_PublicAccount::publicAccountStatus($toUsername);
        if($publicAccountStatus['errcode']!='0') {
            $resultStr='';//invalid public account
        }
		else if ($msgType == 'event') {
			$event    = strtolower((string)$postObj->Event);
			$eventKey = (string)$postObj->EventKey;
			$logContent = sprintf("{\"event\":\"%s\", \"eventKey\":\"%s\"}", $event, $eventKey);
			if ($event == 'subscribe') {
				//关注
				$resultStr = self::subscribe($toUsername, $eventKey);
			} elseif ($event == 'unsubscribe') {
				//取消关注
				$resultStr = self::unsubscribe($fromUsername, $eventKey);
			} elseif ($event == 'click') {
				//点击菜单
				$resultStr = self::clickMenu($toUsername, $eventKey, $fromUsername);
			} elseif ($event == 'scan') {
				// pass
				$ticket = (string)$postObj->Ticket;
				$logContent = sprintf("{\"event\":\"%s\", \"eventKey\":\"%s\", \"ticket\":\"%s\"}", $event, $eventKey, $ticket);
			} elseif ($event == 'location'){
				// pass
				$latitude  = (string)$postObj->Latitude;
				$longitude = (string)$postObj->Longitude;
				$precision = (string)$postObj->Precision;
				$logContent = sprintf("{\"event\":\"%s\", \"eventKey\":\"%s\", \"latitude\":\"%s\", \"longitude\":\"%s\", \"precision\":\"%s\"}",
					$event, $eventKey, $latitude, $longitude, $precision);
			}
		}
        elseif ($msgType == "text" || $msgType == "voice" ) {
			$keyword = '';
			switch ($msgType) {
				case 'text':
					$keyword = trim($postObj->Content);
					break;
				case 'voice':
					$keyword = trim($postObj->Recognition);
					break;
			}
            // 判断是否有工单; 如果有，不回复任何内容
            if(XP_BModel_WorkOrder::hasWorkOrder($toUsername,$fromUsername, $msg))
            {
                XP_Lib_Utility::log('User ' . $fromUsername . ' has Work Order, so keyword ' . $keyword . ' return nothing','Callback');
                $resultStr='';
            }
			else if (!empty($keyword)) {
				$logContent = sprintf("{\"text\":\"%s\"}",str_replace("\"","",$keyword));
				$resultStr = self::getKeyword($toUsername, $keyword, $fromUsername);
			}
		}
        XP_Lib_Utility::log( "type:" . $msgType."\nevent:".$event."\neventKey:".$eventKey."\nkeyword:".$keyword. "\nfrom:". $fromUsername. "\nto:". $toUsername,"Callback");

        $result = "";
        $weixin = new XP_Lib_Weixin();
		XP_Lib_Utility::log(json_encode($resultStr["type"]));
        if (is_array($resultStr)) {
			if(isset($resultStr["type"])&&$resultStr["type"]==3)
			{
				$result=$weixin->getVoiceCallback($fromUsername, $toUsername, $resultStr["mediaId"]);
			}
			else if(isset($resultStr["type"])&&$resultStr["type"]==4)
			{
				$result=$weixin->getVideoCallback($fromUsername, $toUsername, $resultStr["mediaId"]);
			}
			else
            $result=$weixin->getNewsCallback($fromUsername, $toUsername, $resultStr);
		} else if($resultStr) {
            $result=$weixin->getTextCallback($fromUsername, $toUsername, $resultStr);
		}


		/** 微信callback 回调配置好的商家接口 */
        if(empty($logContent)) $logContent = $msg;
		$handlerContent = XP_BModel_CallbackHandler::commonHandler($toUsername, $fromUsername, $msgType, $logContent, $result);
        if(!is_null($handlerContent))
        {
            $result = $weixin->getTextCallback($fromUsername, $toUsername, $handlerContent);
        }

		/*微信消息记录*/
		$bll = new XP_BModel_MessageRecord();
		$model = new XP_Model_MessageRecord();
		$model->fromUser=$fromUsername;
		$model->toUser=$toUsername;
		$model->messageType=array_key_exists($msgType, self::$messageType) ? self::$messageType[$msgType] : self::$messageType['unknow'];
		$model->content=$logContent;
		$model->note="";
		$model->status=1;
		$model->type=1;
		$model->operator=0;
		$model->createTime=date("Y-m-d H:i:s");
		$model->updateTime=date("Y-m-d H:i:s");
		$bll->save($model);

		$model->fromUser=$toUsername;
		$model->toUser=$fromUsername;
		$model->content=is_array($resultStr)?json_encode($resultStr):sprintf("{\"text\":\"%s\"}",str_replace("\"","",$resultStr));
		$bll->save($model);
        return $result;
	}

	public static function subscribe($originalId, $eventKey)
	{
		//@todo 增加关注后续功能
        $result = '';
        $publicAccount = XP_BModel_PublicAccount::getPublicAccount("",$originalId);
        if(empty($publicAccount)) throw new Exception("PublicAccount not found,originalId:".$originalId);
        $result=XP_BModel_Reply::getReply(XP_BModel_Reply::REPLY_TYPE_FOLLOW,$publicAccount->id);
        if(!empty($result)) {
            if(!is_array($result)) $result=get_object_vars($result);
            if($result["type"]=="1") {
                $result=json_decode($result["content"],true);
                $result=XP_Lib_Utility::contentFix($result["text"]);
            }
            else
                $result=json_decode(XP_Lib_Utility::contentFix($result["content"]),true);
        }
		return $result;
	}

	public static function unsubscribe($openid, $eventKey)
	{
		//@todo 取消关注后续功能
		return '';
	}

	public static function getKeyword($originalId, $keyword, $openid=null)
	{
        $bll = new XP_BModel_Keyword();
		$result = $bll->getUniqueKeyword($originalId,$keyword);
        if(empty($result)) {
            $publicAccount = XP_BModel_PublicAccount::getPublicAccount("",$originalId);
            if(empty($publicAccount)) throw new Exception("PublicAccount not found,originalId:".$originalId);
            $result=XP_BModel_Reply::getReply(XP_BModel_Reply::REPLY_TYPE_DEFAULT,$publicAccount->id);
        }
        //XP_Lib_Utility::log(json_encode($result));
        if(!empty($result)) {
            if(!is_array($result)) $result=get_object_vars($result);
            if($result["type"]=="1") {
                $result=json_decode($result["content"],true);
                $result=XP_Lib_Utility::contentFix($result["text"]);
            }
            else
                $result=json_decode(XP_Lib_Utility::contentFix($result["content"]),true);


			// callback function
			$holderrs = XP_BModel_CallbackHandler::handler($originalId, $openid, $keyword);
			if(!is_null($holderrs)) $result = $holderrs;
        }
	    return $result;
	}
    public static function clickMenu($originalId, $key, $openid=null)
    {
        $publicAccount = XP_BModel_PublicAccount::getPublicAccount("",$originalId);
        if(empty($publicAccount)) throw new Exception("PublicAccount not found,originalId:".$originalId);
        $bll = new XP_BModel_Menu();
        $menu = $bll->getMenuByKey($publicAccount->id,$key);
        if(empty($menu)) {
            throw new Exception("Menu not found,key:".$key);}
        else {
            $result=self::getKeyword($originalId,$menu->keyword, $openid);

        }
        return $result;
    }

    /*
	public static function getKeywordConfig($keyword, $openid)
	{
		$reply = '';
		//@todo 输入关键词后匹配操作
		switch ($keyword) {
			case '客服电话':
				$reply = '欢迎拨打商服电话：4001-100-100';break;
			case '首页':
				$reply = array(
					array(
						'title' => '飞拓微连通',
						'desc'  => '欢迎来到飞拓微连通，我们的宗旨为您的购车之旅提供优质贴心的服务。',
						'pic'   => 'http://www.fashionmms.com.cn/ct/wx/images/indexpic.jpg',
						'url'   => 'http://www.fashionmms.com.cn/ct/wx/'.$openid.'/index.aspx',
					)
				);break;
			case '经销商查询':
				$reply = array(
					array(
						'title' => '经销商查询',
						'desc'  => '找到我们的4S店了吗？赶快点击，我来告诉您附近的4S店，欢迎到店咨询哦！',
						'pic'   => 'http://www.fashionmms.com.cn/ct/wx/images/jingxiaoshangpic.jpg',
						'url'   => 'http://www.fashionmms.com.cn/ct/wx/'.$openid.'/jingxiaoshangchaxun.aspx',
					)
				);break;
			case '微访谈':
				$reply = array(
					array(
						'title' => '东风标致最新动态',
						'desc'  => '东风标致更多新闻动态',
						'pic'   => 'http://www.fashionmms.com.cn/ct/wx/images/weifangtanpic.jpg',
						'url'   => 'http://www.fashionmms.com.cn/ct/wx/'.$openid.'//weifangtan.aspx',
					)
				);break;
			case '试驾抢位抽大奖':
				$reply = array(
					array(
						'title' => '试驾抢位抽大奖',
						'desc'  => '东风标致408将在北京举办试乘试驾招募活动，抢先报名有机会参加抽奖，50台mini ipad在等你。赶快来参加吧！',
						'pic'   => 'http://www.fashionmms.com.cn/ct/wx/images/choujiangpic.jpg',
						'url'   => 'http://www.fashionmms.com.cn/ct/wx/'.$openid.'/choujiang.aspx',
					)
				);break;
			case '活动促销':
				$reply = array(
					array(
						'title' => '最新活动',
						'desc'  => '想要看看您心仪的车型是不是降价了？又有哪些新车上市了？赶快点击查看吧',
						'pic'   => 'http://www.fashionmms.com.cn/ct/wx/images/huodongpic.jpg',
						'url'   => 'http://www.fashionmms.com.cn/ct/wx/'.$openid.'/huodong.aspx',
					)
				);break;

		}
		if (!empty($reply)) {
			return $reply;
		}
		return '';
	}

	public static function clickMenu($openId, $key)
	{
		$reply = '';

		switch ($key) {
			case 'CLICK_ZXHD':
				$reply = array(
					array(
						'title' => '最新活动',
						'desc'  => '想要看看您心仪的车型是不是降价了？又有哪些新车上市了？赶快点击查看吧',
						'pic'   => 'http://www.fashionmms.com.cn/ct/wx/images/huodongpic.jpg',
						'url'   => 'http://www.fashionmms.com.cn/ct/wx/'.$openId.'/huodong.aspx',
					)
				);
				break;
			case 'CLICK_SJZM':
				$reply = array(
					array(
						'title' => '试驾抢位抽大奖',
						'desc'  => '东风标致408将在北京举办试乘试驾招募活动，抢先报名有机会参加抽奖，50台mini ipad在等你。赶快来参加吧！',
						'pic'   => 'http://www.fashionmms.com.cn/ct/wx/images/choujiangpic.jpg',
						'url'   => 'http://www.fashionmms.com.cn/ct/wx/'.$openId.'/choujiang.aspx',
					)
				);
				break;
			case 'CLICK_WFT':
				$reply = array(
					array(
						'title' => '东风标致最新动态',
						'desc'  => '东风标致更多新闻动态',
						'pic'   => 'http://www.fashionmms.com.cn/ct/wx/images/weifangtanpic.jpg',
						'url'   => 'http://www.fashionmms.com.cn/ct/wx/'.$openId.'/weifangtan.aspx',
					)
				);
				break;
			case 'CLICK_RGKF':
				$reply = '欢迎拨打商服电话：4001-100-100';
				break;
			case 'CLICK_JXSCX':
				$reply = array(
					array(
						'title' => '经销商查询',
						'desc'  => '找到我们的4S店了吗？赶快点击，我来告诉您附近的4S店，欢迎到店咨询哦！',
						'pic'   => 'http://www.fashionmms.com.cn/ct/wx/images/jingxiaoshangpic.jpg',
						'url'   => 'http://www.fashionmms.com.cn/ct/wx/'.$openId.'/jingxiaoshangchaxun.aspx',
					)
				);
				break;
			case 'CLICK_HOME':
				$reply = array(
					array(
						'title' => '飞拓微连通',
						'desc'  => '欢迎来到飞拓微连通，我们的宗旨为您的购车之旅提供优质贴心的服务。',
						'pic'   => 'http://www.fashionmms.com.cn/ct/wx/images/indexpic.jpg',
						'url'   => 'http://www.fashionmms.com.cn/ct/wx/'.$openId.'/index.aspx',
					)
				);
				break;
		}

		return $reply;
	}
    */

}