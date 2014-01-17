<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jacky Fan
 * Date: 13-1-7
 * Time: 下午1:16
 * To change this template use File | Settings | File Templates.
 */

class XP_Lib_Weixin
{
	private $appId = '';
	private $appSecret = '';
	private $token = '';
	private $connectTimeout = 30;
	private $tokenLockTimeout = 10;
	private $userInfo;
	private $publicAccountId;
	public static $app = null;
	const MEDIA_TYPE_IMAGE="image";
	const MEDIA_TYPE_VOICE="voice";
	const MEDIA_TYPE_VIDEO="video";
	const MEDIA_TYPE_THUMB="thumb";
    public static function urlRequestWithAccessToken($url,$data,$publicAccountId,$appId,$appSecret) {
        $json=XP_Lib_Utility::urlRequest($url,$data,false);
        if (isset($json['errcode']) ) {
            if($json['errcode'] == '40001') {
                $accessToken=XP_Lib_Weixin::getAccessToken($publicAccountId,$appId,$appSecret);
                $pattern="/(\?|&)(access_token=)([\s\S]*?)($|&)/i";
                $url=preg_replace($pattern, "\${1}\${2}".$accessToken."\${4}", $url);
                $json=XP_Lib_Utility::urlRequest($url,$data,false);
            }
        }
        else  {
            throw new Exception('http requset error, result: '.$json);
        }
        return $json;
    }

	/**
	 * 获取微信版本号
	 * @param $userAgent
	 * @return string|bool
	 */
	public static function checkWeixinVersion($userAgent)
	{
		if (empty($userAgent)) {
			return false;
		}

		if (preg_match('/MicroMessenger\/([0-9,\.])+/i', $userAgent, $matches)) {
			list($client, $version) = explode('/', $matches[0]);

			return $version;
		}

		return false;
	}

	/**
	 * 微信接口入口
	 * @param $config
	 * @return null|XP_Lib_Weixin
	 */
	public static function portal($config)
	{
		if (is_null(self::$app)) {
			$obj                = new XP_Lib_Weixin();
			$obj->appId         = isset($config['appId']) ? $config['appId'] : '';
			$obj->appSecret     = isset($config['appSecret']) ? $config['appSecret'] : '';
			$obj->token         = isset($config['token']) ? $config['token'] : '';
			$obj->loginHost     = 'http://login.weixin.qq.com';
			$obj->apiHost       = 'http://api.weixin.qq.com';
			$obj->apiSecretHost = 'https://api.weixin.qq.com';
			$obj->publicAccountId = isset($config['publicAccountId']) ? $config['publicAccountId'] : '';
			self::$app          = $obj;
		}

		return self::$app;
	}

	public function valid($publicAccountId,$signature, $timestamp, $nonce)
	{
		return $this->checkSignature($publicAccountId,$signature, $timestamp, $nonce);
	}

	public function getAuthorizationUrl($redirectUri, $scope = 'snsapi_base')
	{
		$redirect = 'https://open.weixin.qq.com/connect/oauth2/authorize' .
			'?appid=' . $this->appId .
			'&redirect_uri=' . urlencode($redirectUri) .
			'&response_type=code' .
			'&scope=' . $scope .
			'&state=get_user_base' .
			'#wechat_redirect';

		return $redirect;
	}

	/**
	 * 获取用户accessToken信息
	 * @param $code
	 * @return array
	 */
	public function getUserAccessToken($code)
	{
		if (empty($code)) {
			throw new Exception('authorization code empty.');
		}
		$url    = 'https://api.weixin.qq.com/sns/oauth2/access_token' .
			'?appid=' . $this->appId .
			'&secret=' . $this->appSecret .
			'&code=' . $code .
			'&grant_type=authorization_code';
		$return = $this->http('get', $url);
		if (empty($return['openid'])) {
			throw new Exception('get user openId error');
		}

		return $return;
	}

	/**
	 * 获取用户信息
	 * @param $accessToken
	 * @param $openid
	 * @return mixed
	 * @throws Exception
	 */
	public function getUserInfo($openid)
	{
		if (empty($openid)) {
			throw new Exception('[getUserInfo] user openid empty.');
		}
		if (empty($accessToken)) {
			$accessToken = $this->getAccessToken($this->publicAccountId,$this->appId, $this->appSecret);
		}
		$url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $accessToken . '&openid=' . $openid;

		$this->userInfo[$openid] = $this->http('get', $url);

		return $this->userInfo[$openid];
	}

	/**
	 * 获取文字类型callback
	 * @param $fromUsername
	 * @param $toUsername
	 * @param $time
	 * @param $contentStr
	 * @return string
	 */
	public function getTextCallback($fromUsername, $toUsername, $contentStr)
	{
		$time    = time();
		$textTpl = "<xml>";
		$textTpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
		$textTpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
		$textTpl .= "<CreateTime>%s</CreateTime>";
		$textTpl .= "<MsgType><![CDATA[text]]></MsgType>";
		$textTpl .= "<Content><![CDATA[%s]]></Content>";
		$textTpl .= "<FuncFlag>0</FuncFlag>";
		$textTpl .= "</xml>";
		return sprintf($textTpl, $fromUsername, $toUsername, $time, $contentStr);
	}
	public function getVoiceCallback($fromUsername, $toUsername, $mediaId)
	{
		$time    = time();
		$textTpl = "<xml>";
		$textTpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
		$textTpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
		$textTpl .= "<CreateTime>%s</CreateTime>";
		$textTpl .= "<MsgType><![CDATA[voice]]></MsgType>";
		$textTpl .= "<Voice><MediaId><![CDATA[%s]]></MediaId></Voice>";
		$textTpl .= "</xml>";
		return sprintf($textTpl, $fromUsername, $toUsername, $time, $mediaId);
	}
	public function getVideoCallback($fromUsername, $toUsername, $mediaId,$title="视频推荐",$desc="视频推荐")
	{
		$time    = time();
		$textTpl = "<xml>";
		$textTpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
		$textTpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
		$textTpl .= "<CreateTime>%s</CreateTime>";
		$textTpl .= "<MsgType><![CDATA[video]]></MsgType>";
		$textTpl .= "<Video>";
		$textTpl .= "<MediaId><![CDATA[%s]]></MediaId>";
		$textTpl .= "<Title><![CDATA[%s]]></Title>";
		$textTpl .= "<Description><![CDATA[%s]]></Description>";
		$textTpl .= "</Video></xml>";
		return sprintf($textTpl, $fromUsername, $toUsername, $time, $mediaId,$title,$desc);
	}


	public function getNewsCallback($fromUsername, $toUsername, $articles)
	{
		$time    = time();
		$newsTpl = "<xml>";
		$newsTpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
		$newsTpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
		$newsTpl .= "<CreateTime>%s</CreateTime>";
		$newsTpl .= "<MsgType><![CDATA[news]]></MsgType>";
		$newsTpl .= "<Content><![CDATA[]]></Content>";
		$newsTpl .= "<ArticleCount>%d</ArticleCount>";
		$newsTpl .= "<Articles>%s</Articles>";
		$newsTpl .= "<FuncFlag>1</FuncFlag>";
		$newsTpl .= "</xml>";
		$newsArticleTpl = "<item>";
		$newsArticleTpl .= "<Title><![CDATA[%s]]></Title>";
		$newsArticleTpl .= "<Description><![CDATA[%s]]></Description>";
		$newsArticleTpl .= "<PicUrl><![CDATA[%s]]></PicUrl>";
		$newsArticleTpl .= "<Url><![CDATA[%s]]></Url>";
		$newsArticleTpl .= "</item>";
		$articleXml = "\n";
		foreach ($articles as $item) {
			$articleXml .= sprintf($newsArticleTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
		}
		return sprintf($newsTpl, $fromUsername, $toUsername, $time, count($articles), $articleXml);
	}

	/**
	 * 发送模板消息
	 * @param string $openId
	 * @param string $templateId
	 * @param array  $data
	 * @return bool
	 */
	public function sendTemplateMsg($msg)
	{
		if (empty($msg['touser'])) {
			throw new Exception('openid not empty.');
		}
		if (empty($msg['template_id'])) {
			throw new Exception('template_id not empty.');
		}
		if (empty($msg['data'])) {
			throw new Exception('data not empty.');
		}

        $accessToken = $this->getAccessToken($this->publicAccountId, $this->appId, $this->appSecret);
		if (empty($accessToken)) {
			throw new Exception('get access_token error.');
		}
		$url  = $this->apiSecretHost . '/cgi-bin/message/template/send?access_token=' . $accessToken;
		$body = $this->encodeJson($msg);
		$json = $this->http('post', $url, $body);

		if ($json['errcode'] == 0) {
			return true;
		}

		return false;
	}

	/**
	 * 发送客服消息
	 * @param string $openId
	 * @param string $templateId
	 * @param array  $data
	 * @return bool
	 */
	public function sendCustomMsg($msg)
	{
		if (empty($msg['touser'])) {
			throw new Exception('openid not empty.');
		}

		$accessToken = $this->getAccessToken($this->publicAccountId, $this->appId, $this->appSecret);
		if (empty($accessToken)) {
			throw new Exception('get access_token error.');
		}
		$url  = $this->apiSecretHost . '/cgi-bin/message/custom/send?access_token=' . $accessToken;
		$body = $this->encodeJson($msg);
		$json = $this->http('post', $url, $body);

		if ($json['errcode'] == 0) {
			return true;
		}

		return false;
	}

	/**
	 * 微信接口合并模板和内容，组成消息体验证模板有效性
	 * @param $template
	 * @param $data
	 * @return array
	 * @throws Exception
	 */
	public function combineTemplate($template, $data)
	{
		$accessToken = $this->getAccessToken($this->publicAccountId, $this->appId, $this->appSecret);
		if (empty($accessToken)) {
			throw new Exception('get access_token error.');
		}
		$url  = $this->apiSecretHost . '/cgi-bin/message/template/combine?access_token=' . $accessToken;
		$body = $this->encodeJson(array('message' => $template, 'data' => $data));
		$json = $this->http('post', $url, $body);

		return $json['result'];
	}

	/**
	 * 发送文本消息
	 * @param $openId
	 * @param $text
	 * @return string
	 */
	public function sendTextMsg($openId, $text)
	{
		if (empty($openId)) {
			throw new Exception('openid not empty.');
		}
		if (empty($text)) {
			throw new Exception('text not empty.');
		}
		$contents = array(
			'content' => $text,
		);

		return $this->sendMsg($openId, 'text', $contents);
	}

	/**
	 * 发送图片
	 * @param $openId
	 * @param $imageFile
	 * @return string
	 */
	public function sendImageMsg($openId, $mediaId, $imageFile = "")
	{
		if (empty($openId)) {
			throw new Exception('openid not empty.');
		}

		//上传图片获得 media_id
		if (!empty($imageFile)) {
			$media   = $this->updateMedia($openId, 'image', $imageFile);
			$mediaId = $media['media_id'];
		}

		//发送消息
		return $this->sendMsg($openId, 'image', array(
			'media_id' => $mediaId,
		));
	}

	/**
	 * 发送语音
	 * @param $openId
	 * @param $voiceFile
	 * @return string
	 */
	public function sendVoiceMsg($openId, $voiceFile = '', $mediaId = 0)
	{
		if (empty($openId)) {
			throw new Exception('openid not empty.');
		}

		//上传语音获得 media_id
		if ($voiceFile != '') {
			$media   = $this->updateMedia($openId, 'voice', $voiceFile);
			$mediaId = $media['media_id'];
		}

		//发送消息
		return $this->sendMsg($openId, 'voice', array(
			'media_id' => $mediaId,
		));
	}

	/**
	 * 发送视频
	 * @param $openId
	 * @param $videoFile
	 * @return string
	 */
	public function sendVideoMsg($openId, $videoFile = '', $mediaId = 0, $thumbMediaId = 0)
	{
		if (empty($openId)) {
			throw new Exception('openid not empty.');
		}

		//上传视频获得 media_id
		if ($videoFile != '') {
			$media   = $this->updateMedia($openId, 'video', $videoFile);
			$mediaId = $media['media_id'];
		}

		//发送消息
		return $this->sendMsg($openId, 'video', array(
			'media_id'       => $mediaId,
			"thumb_media_id" => $thumbMediaId
		));
	}

	/**
	 * 发送链接
	 * @param        $openId
	 * @param        $title  标题
	 * @param        $description  描述
	 * @param        $url  链接地址
	 * @param string $imageFile  缩略图
	 */
	public function sendLinkMsg($openId, $title, $description, $url, $imageFile = '', $thumbMediaId = 0)
	{
		if (empty($openId)) {
			throw new Exception('openid not empty.');
		}
		if (empty($url)) {
			throw new Exception('url not empty.');
		}
		if (empty($title)) {
			throw new Exception('title not empty.');
		}
		//上传缩略图获得 thumb_media_id
		//$thumbMediaId = 0;
		if ($imageFile != '') {
			//调用上传文件接口
			$media        = $this->updateMedia($openId, 'image', $imageFile);
			$thumbMediaId = $media['media_id'];
		}

		//发送消息
		return $this->sendMsg($openId, 'link', array(
			'title'          => $title,
			'description'    => $description,
			'url'            => $url,
			'thumb_media_id' => $thumbMediaId,
		));
	}

	/**
	 * 给微信发送消息
	 * @param $openId
	 * @param $type   消息类型 text 文本；image 图片；voice 语音；video 视频；link 链接
	 * @param $text
	 * @return string
	 */
	private function sendMsg($openId, $type, $contents)
	{
		$accssToken = $this->getAccessToken($this->publicAccountId, $this->appId, $this->appSecret);
		//debug($accssToken);
		if (empty($accssToken)) {
			throw new Exception('get access_token error.');
		}
		$url  = 'http://api.weixin.qq.com/cgi-bin/message/send?access_token=' . $accssToken;
		$body = $this->encodeJson(array(
			'touser'  => $openId,
			'msgtype' => $type,
			$type     => $contents,
		));
		$json = $this->http('post', $url, $body);

		if ($json['errcode'] == 0) {
			return true;
		}

		return false;
	}

	private function encodeJson($arr, $parentKey = '')
	{
		$ret = array();
		foreach ($arr as $key => $value) {
			if (is_array($value)) {
				if (is_int($key)) {
					$ret[] = $this->encodeJson($value, $key);
				} else {
					$ret[] = '"' . $key . '":' . $this->encodeJson($value, $key);
				}
			} else {
				//$value = str_replace('{', '\{', $value);
				//$value = str_replace('}', '\}', $value);
				//$value = str_replace('[', '\[', $value);
				//$value = str_replace(']', '\]', $value);
				//$value = str_replace(',', '\,', $value);
				//$value = str_replace(':', '\:', $value);
				$value  = str_replace('"', '\"', $value);
				$ret [] = '"' . $key . '":"' . $value . '"';
			}
		}
		if (substr($parentKey, -6) !== 'button') {
			$retStr = '{' . join(',', $ret) . '}';
		} else {
			$retStr = '[' . join(',', $ret) . ']';
		}

		return $retStr;
	}

	/**
	 * 上传多媒体文件
	 * @param $openId
	 * @param $type  文件类型 image 图片；voice 语音；video 视频；thumb 缩略图
	 * @param $file  文件本地路径
	 */
	public function updateMedia( $type, $file)
	{

		if (empty($type)) {
			throw new Exception('type not empty.');
		}
		if (empty($file)) {
			throw new Exception('file not empty.');
		}
        $accssToken = $this->getAccessToken($this->publicAccountId,$this->appId, $this->appSecret);
		if (empty($accssToken)) {
			throw new Exception('get access_token error.');
		}
		$url = 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $accssToken . '&type=' . $type;
		return $this->http('post', $url, array(
			'media' => '@' . $file,
		));
	}

	private function checkSignature($publicAccountId,$signature, $timestamp, $nonce)
	{
        if(empty($publicAccountId)) {
            $config    = System_Lib_App::app()->getConfig('wxConfig');
            $token=$config["token"];
        }
        else {
            $publicAccount = XP_BModel_PublicAccount::getPublicAccount($publicAccountId);
            if(empty($publicAccount)) throw new Exception("publicAccount not found,id:".$publicAccountId);
            $token  = $publicAccount->token;
        }
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode($tmpArr);
		$tmpStr = sha1($tmpStr);
		if ($tmpStr == $signature) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 创建对话菜单
	 * @param $data
	 * @return bool
	 */
	public function createMenu($data_,$publicAccountId,$appId,$appSecret)
	{
        $errcode="0";
        $errmsg="操作成功";
        $data="";
        try {
            $accessToken = $this->getAccessToken($publicAccountId,$appId,$appSecret);
            if (empty($accessToken))throw new Exception('get access_token error.');
            $webservice = System_Lib_App::app()->getConfig('webservice');
            $url = sprintf($webservice['createMenu'],$accessToken);
            $json = XP_Lib_Weixin::urlRequestWithAccessToken($url,$data_,$publicAccountId,$appId,$appSecret);
            if (isset($json['errcode']) && $json['errcode']=='0') {
                $errcode='0';
                $errmsg='create menu success';
            }
            else {
                $errcode=$json['errcode'];
                $errmsg=$json['errmsg'];
            }
        }
        catch(Exception $e) {
            $errcode="9999";
            $errmsg=sprintf("get keyword page error,params:%s,message:%s",json_encode(func_get_args()),$e->getMessage());
        }
        $r=XP_Lib_Utility::jsonResult($errcode,$errmsg,$data);
        $r=json_decode($r,true);
        return $r;
	}

	/**
	 * 删除菜单
	 * @return bool
	 */
	public function deleteMenu()
	{
        $accssToken = $this->getAccessToken($this->publicAccountId,$this->appId, $this->appSecret);
		//debug($accssToken);
		if (empty($accssToken)) {
			throw new Exception('get access_token error.');
		}
		$url  = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=' . $accssToken;
		$json = $this->http('get', $url);
		if ($json['errcode'] == 0) {
			return true;
		}

		return false;
	}

	/**
	 * 获取菜单
	 * @return bool
	 */
    /*
	public function getMenu()
	{
        $accssToken = $this->getAccessToken($this->publicAccountId,$this->appId, $this->appSecret);
		//debug($accssToken);
		if (empty($accssToken)) {
			throw new Exception('get access_token error.');
		}
		$url  = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=' . $accssToken;
		$json = $this->http('get', $url);
		if (!empty($json['menu'])) {
			return $json['menu'];
		}

		return false;
	}
*/

	/**
	 * GET AccessToken From Weixin API
	 */
	public function getAccessToken($publicAccountId,$appId,$appSecret)
	{
		$key      = XP_Lib_KeyManager::GetWeixinAccessTokenKey().$publicAccountId;
		$redisCfg = System_Lib_App::app()->getConfig('redis');
		$repeat   = 10;
		$count    = 0;
		do {
			$accessToken = System_Lib_Redis::Get($key, $redisCfg, false);
            $accessToken = false;
			if ($accessToken === false) {
				XP_Lib_Utility::log( 'no cached accessToken','wxapi');
				$lockKey = XP_Lib_KeyManager::GetWeixinAccessTokenLockKey().$publicAccountId;
				//查询是否可以重新生成 accessToken
				if (System_Lib_Redis::SetIfNotExist($lockKey, 1, $redisCfg, false, $this->tokenLockTimeout)) {
                    XP_Lib_Utility::log( 'create accessToken begin','wxapi');
					try {
						$accessToken = $this->createAccessToken($publicAccountId,$appId,$appSecret);
					} catch (Exception $e) {
                        XP_Lib_Utility::log( 'create accessToken error. ' . $e->getMessage(),'wxapi');
					}
					//清空锁定
					System_Lib_Redis::Delete($lockKey, $redisCfg);
					if ($accessToken != false) {
						return $accessToken;
					}
				} else {
                    XP_Lib_Utility::log(  'create accessToken lock ' . $count,'wxapi');
				}
				usleep(500000);
			} else {
                XP_Lib_Utility::log( 'get cached accessToken = ' . $accessToken,'wxapi');
			}
			$count++;
		} while ($accessToken === false && $count < $repeat);

		return $accessToken;
	}

	public function createAccessToken($publicAccountId,$appId,$appSecret)
	{
        $webservice = System_Lib_App::app()->getConfig('webservice');
        $url = sprintf($webservice['getAccessToken'],$appId,$appSecret);
		$json = XP_Lib_Utility::urlRequest($url);
		if ($json !== false) {
            $accessToken = $json['access_token'];
			$ttl   = $json['expires_in'];
			$this->accessTokenSaveCache($publicAccountId,$appId,$accessToken, $ttl);
		} else {
			throw new Exception('connect error.');
		}
		return $accessToken;
	}

	public function accessTokenDeleteCache()
	{
		$key    = XP_Lib_KeyManager::GetWeixinAccessTokenKey();
		$server = System_Lib_App::app()->getConfig('redis');
		System_Lib_Redis::Delete($key, $server);
	}

	public function accessTokenSaveCache($publicAccountId,$appId,$accessToken, $ttl)
	{
        XP_Lib_Utility::log('accessTokenSaveCache accessToken = ' . $accessToken . ', expires_in = ' . ($ttl - 100),'wxapi');
		$key    = XP_Lib_KeyManager::GetWeixinAccessTokenKey().$publicAccountId;
		$server = System_Lib_App::app()->getConfig('redis');
		System_Lib_Redis::Set($key, $accessToken, $server, false, $ttl - 100);
        $accessToken = System_Lib_Redis::Get($key, $server, false);
	}

	/**
	 * 访问接口
	 * @param        $method  协议
	 * @param        $url  地址
	 * @param string $body  内容，如果不为空，则发送post请求
	 * @return array
	 */
	private function http($method, $url, $body = '')
	{
		$resp           = '';
		$wxapiStartTime = microtime(true);
		$repeat         = 3;
		$count          = 1;
		$httpParam      = array(//CURLOPT_SSL_VERIFYPEER	=> false,
			//CURLOPT_SSL_VERIFYHOST	=> false,
			//CURLOPT_USERAGENT		=> 'Weituangou Client',
		);
		if (strtoupper($method) == 'POST') {
			$httpParam[CURLOPT_POST] = true;
			if (!empty($body)) {
				$httpParam[CURLOPT_POSTFIELDS] = $body;
			}
		}
		while ($count <= $repeat) {
			$return = System_Lib_ServerAccessor::CallCURL($url, $httpParam, $resp, array(), $this->connectTimeout);
            XP_Lib_Utility::log('Response : ' . $resp,'XP_Lib_Weixin');
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
				$json = json_decode($resp, true);
				if (isset($json['errcode']) && $json['errcode'] != 0) {
					if ($json['errcode'] == '40001') {
						//删除 accessToken
						$this->accessTokenDeleteCache();
					} else {
						throw new Exception($json['errmsg'] . '[' . $json['errcode'] . ']');
					}
				}
				if (isset($json['error']) && $json['error'] != '') {
					throw new Exception($json['error']);
				}

				return $json;
			}
			$count++;
			usleep(500000);
		}
		throw new Exception('connect error.');
	}
}