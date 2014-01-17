<?php

class XP_BModel_WorkOrder
{
    public static $isDecodeData = false;
    public static $platformId = null;
    public static $publicSalt = null;
	/**
	 * 判断用户是否有工单
	 * @return array|Exception
	 */
	public static function hasWorkOrder($originalId,$openid, $msg)
	{
        $ret = false;

        $webservice = System_Lib_App::app()->getConfig('webservice');
        $url = $webservice['hasWorkOrder'];
        $mapi = System_Lib_App::app()->getConfig('mapi');
        self::$platformId = $mapi['id'];
        self::$publicSalt = $mapi['secret'];

        // call mapi
        $params = array(
            'clientId'      => self::$platformId,
            'version'       => '2.0',
            'clientVer'     => '2.1',
            'wxRequest' => $msg
        );

        $publicAccount = XP_BModel_PublicAccount::getPublicAccount("",$originalId);
        if(!empty($publicAccount) && $publicAccount->customerService==1) {
            try{
                $worder = self::getMapi($url, $params);
                if (isset($worder['retCode']) && $worder['retCode'] == -300001)
                {
                    $ret = true;
                }
            }catch (Exception $e) {
                if($e->getCode() == -300001) $ret = true;
            }
        }
		return $ret;
	}
    private static function getMapi($url,$params) {
        $params=self::getClientFlagParams($params);
        if (isset($params) && is_array($params)) {
            $params = http_build_query($params); //ADD
        }
        $result = XP_Lib_Utility::urlRequest($url,$params ,false);
        if (!empty($result)) {
            if (!isset($result['retCode'])) {
                XP_Lib_Utility::log( "MAPI ERROR || retCode not isset",'mapi');
                throw new Exception($result['retMsg'], $result['retCode']);
            }
            else if ($result['retCode'] != 0) {
                XP_Lib_Utility::log( "MAPI ERROR || retCode not 0(".$result['retCode'].')','mapi');
                throw new Exception($result['retMsg'], $result['retCode']);
            }
            //is decrypt
            if (self::$isDecodeData == true && is_array($result) && !empty($result['retData'])) {
                $result['retData'] = System_Lib_Utils::base64_url_decode($result['retData']);
                $result['retData'] = System_Lib_AES::decrypt($result['retData'], self::$publicSalt);
                $result['retData'] = json_decode($result['retData'], true);
            }
            XP_Lib_Utility::log(json_encode($result));
            return $result;
        }
        else {
            XP_Lib_Utility::log( "MAPI ERROR || httpSend return 0",'mapi');
            throw new Exception('Unkonw exception', 9999);
        }
    }

    /**
     * 是否需要解密:
     * 1、微团购的salt const AES_MAPI_KEY = ':ec)if#<{*123%I1w4s&3seU';
     * 2、非微团购   uin倒着取-19位作为key 连接上 salt 倒着取 -5 方法为：self::getMobileClientKey()
     * 3、基础接口明码传递时不需要skey,uin，
     * 4、如果需要加密的接口要注意：skey和cdata平级传递，uin放到cdata中加密
     */
    private static function getClientFlagParams($params)
    {
        //mustParams
        $mustParams = array(
            'clientId'         => !empty($params['clientId']) ? $params['clientId'] : self::$platformId,
            'version'          => !empty($params['version']) ? $params['version'] : '2.0',
            'clientVer'        => !empty($params['clientVer']) ? $params['clientVer'] : '2.1',
            'skey'             => !empty($params['skey']) ? $params['skey'] : '',
        );
        //创建params 删除明码，用于cdata
        if (!empty($params['clientId'])) unset($params['clientId']);
        if (!empty($params['version'])) unset($params['version']);
        if (!empty($params['clientVer'])) unset($params['clientVer']);
        if (!empty($params['skey'])) unset($params['skey']);

        if (!empty($mustParams['skey'])) unset($params['skey']);
        $aesParams = array();
        $cdata = System_Lib_AES::encrypt(http_build_query($params), self::$publicSalt);
        $aesParams['cdata'] = System_Lib_Utils::base64_url_encode($cdata);
        self::$isDecodeData = true;

        $params = array_merge($mustParams, $aesParams);
        return $params;

    }
}