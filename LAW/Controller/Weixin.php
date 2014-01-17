<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-10-30
 * Time: 上午10:37
 * To change this template use File | Settings | File Templates.
 */

class XP_Controller_Weixin extends  System_Lib_Controller {

    protected $layoutName = null;

    public function callbackAction()
    {
        $startTime = microtime(true);
        $echoStr   = System_Lib_App::app()->get("echostr", System_Lib_Request::TYPE_STRING);
        $config    = System_Lib_App::app()->getConfig('wxConfig');
        $msg       = file_get_contents('php://input');
        $return    = '';

        if (!empty($echoStr)) {
            //验证接口
            $signature = System_Lib_App::app()->get("signature", System_Lib_Request::TYPE_STRING);
            $timestamp = System_Lib_App::app()->get("timestamp", System_Lib_Request::TYPE_STRING);
            $nonce     = System_Lib_App::app()->get("nonce", System_Lib_Request::TYPE_STRING);
            $publicAccountId     = System_Lib_App::app()->get("PublicAccountId", System_Lib_Request::TYPE_STRING);
            $weixin = new XP_Lib_Weixin();
            if ($weixin->valid($publicAccountId,$signature, $timestamp, $nonce) == true) {
                $return = $echoStr;
            }
        } else {
            //消息接口
            $return = XP_BModel_Callback::receive($msg);
        }
        $log = str_replace("\n", '', $msg);
        $log = str_replace("\r", '', $log);
        XP_Lib_Utility::log("SpendTime:".System_Lib_Utils::getExcuteTime($startTime) ."\nUrl:" . System_Lib_App::app()->request()->getUri() . "\nContent:" . $log . "\nReturn:" . $return,"wxapi");
        echo $return;
    }

    public function sendMessageAction()
    {
        $merchantId = System_Lib_App::app()->getRequest('MerchantId', System_Lib_Request::TYPE_INT);
        $openId     = System_Lib_App::app()->getRequest('OpenId', System_Lib_Request::TYPE_STRING);
        $templateId = System_Lib_App::app()->getRequest('TemplateId', System_Lib_Request::TYPE_INT);
        $nickName   = System_Lib_App::app()->getRequest('NickName', System_Lib_Request::TYPE_STRING, '用户');
        $hostName   = System_Lib_App::app()->getRequest('HostName', System_Lib_Request::TYPE_STRING);
        $activity   = System_Lib_App::app()->getRequest('Activity', System_Lib_Request::TYPE_STRING);
        $code       = System_Lib_App::app()->getRequest('Code', System_Lib_Request::TYPE_STRING);
        $callback   = System_Lib_App::app()->getRequest('Callback', System_Lib_Request::TYPE_STRING);
        $time       = System_Lib_App::app()->getRequest('Time', System_Lib_Request::TYPE_INT);
        $sign       = System_Lib_App::app()->getRequest('Sign', System_Lib_Request::TYPE_STRING);
        $signKey    = XP_Lib_FeiTuo::SIGN_KEY; //34d9hrsdIyr378YG763J

        if (empty($merchantId)) {
            return $this->messageRender(XP_Lib_Error::WX_MSG_MERCHANTID_EMPTY);
        }

        if (empty($openId)) {
            return $this->messageRender(XP_Lib_Error::WX_MSG_OPENID_EMPTY);
        }

        if (empty($templateId)) {
            return $this->messageRender(XP_Lib_Error::WX_MSG_TEMPLATEID_EMPTY);
        }

//		if (empty($nickName)) {
//			return $this->messageRender(XP_Lib_Error::WX_MSG_NICKNAME_EMPTY);
//		}

        if (empty($hostName)) {
            return $this->messageRender(XP_Lib_Error::WX_MSG_HOSTNAME_EMPTY);
        }

        if (empty($activity)) {
            return $this->messageRender(XP_Lib_Error::WX_MSG_ACTIVITY_EMPTY);
        }

        if (empty($code)) {
            return $this->messageRender(XP_Lib_Error::WX_MSG_CODE_EMPTY);
        }

        if (empty($time)) {
            return $this->messageRender(XP_Lib_Error::WX_MSG_TIME_EMPTY);
        }

        if (empty($sign)) {
            return $this->messageRender(XP_Lib_Error::WX_MSG_SIGN_EMPTY);
        }

        $wxConfig = System_Lib_App::app()->getConfig('wxConfig');

        $signString = $merchantId.$activity.$callback.$code.$hostName.$openId.$templateId.$time;
        debug($signString.$signKey);
        $signInput = md5($signString.$signKey);
        debug($signInput);
        if (strtoupper($signInput) != strtoupper($sign)) {
            return $this->messageRender(XP_Lib_Error::WX_MSG_SIGN_VERIFY_ERROR);
        }

        if (($tempCode = XP_Lib_FeiTuo::getTemplateCode($templateId)) == '') {
            return $this->messageRender(XP_Lib_Error::WX_MSG_TEMPLATE_NOT_FOUND);
        }

        $msg = array(
            'touser' => $openId,
            'template_id' => $tempCode,
            'data' => array(
                'Des' => array(
                    'NickName' => $nickName,
                    'HostName' => $hostName,
                    'Activity' => $activity,
                    'Code' => $code,
                    'Callback' => $callback,
                )
            )
        );
        debug($msg);

        try {
            $result = XP_Lib_Weixin::portal($wxConfig)->sendTemplateMsg($msg);
            if ($result) {
                //success
                $this->messageRender(0);
            }
        } catch (Exception $e) {
            //failure
            $errorMsg = 'ErrorCode: '.$e->getCode().', ERROR: ' . $e->getMessage();
            XP_Lib_Log::debug('wxapi', $errorMsg);
            $errorCode = XP_Lib_Error::WX_MSG_SEND_MESSAGE_ERROR;

            $this->messageRender($errorCode, 'wxapi Error. '.$errorMsg);
        }
    }

    /**
     * 创建某AppID下的菜单
     */
    public function createMenuAction()
    {
        $errcode    = 0;
        $errmsg = '';
        $data = '';

        $publicAccountId = System_Lib_App::app()->getRequest('publicAccountId', System_Lib_Request::TYPE_INT);
        try {
            $menu = new XP_BModel_Menu();
            $menus  = $menu->getMenu($publicAccountId);
            $menusXml  = $menu->getMenuJson($menus);
            $publicAccount=XP_BModel_PublicAccount::getPublicAccount($publicAccountId);
            if(empty($publicAccount)) throw new Exception("PublicAccount not found,publicAccountId:".$publicAccountId);
            $weixin=new XP_Lib_Weixin();
            $result=$weixin->createMenu($menusXml,$publicAccount->id,$publicAccount->appId,$publicAccount->appSecret);
            $errcode=$result['errcode'];
            $errmsg=$result['errmsg'];
            $data=$result['data'];
        } catch (Exception $e) {
            $errcode="9999";
            $errmsg="Create menu error.Error: " . $e->getMessage();
        }
        print_r(XP_Lib_Utility::jsonResult($errcode, $errmsg, $data));
    }
}