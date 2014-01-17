<?php
/**
 * 
 * Mar 30, 2013 4:17:24 PM 
 *
 * @author Kevin.Hu, Inc. <huwenhua@group-net.cn>
 * @version v2.0
 */

class XP_Controller_Notify extends System_Lib_Controller
{
    public function accessTokenAction(){
        // 1. 获得必须的参数
        $originalId='';
        $cdata = System_Lib_App::getPost('cdata', System_Lib_Request::TYPE_STRING);
        if (empty($_POST)){
            $cdata = System_Lib_App::get('cdata', System_Lib_Request::TYPE_STRING);
        }
        try{
            $params = $this->decodeParams($cdata);
            MPApi_Lib_Log::debug('notify', http_build_query($params) . 'encode: ' . $cdata);
            $originalId=$params['originalId'];
            if(empty($originalId)) throw new Exception("originalId not found,originalId:$originalId");
        }catch(Exception $e){
            return $this->_retMsgError(MPApi_Lib_Error::MPAPI_PARAM_ERR);
        }
        // 2. 验证请求有效
        $vfuncs = array();
        array_push($vfuncs,'isTimeout');
        array_push($vfuncs,'isClentErr');
        array_push($vfuncs,'isClientversionErr');
        if (! $this->chkRequest($vfuncs, $params))
        {
            return $this->_retMsgError(MPApi_Lib_Error::MPAPI_PARAM_ERR);
        }


        $accessToken = $this->GetWeixinAccessToken($originalId);
        $ret = array();
        if(strlen($accessToken)>0){

            $ret["accessToken"] = $accessToken;
            $jsonData = json_encode($ret);

            $result = System_Lib_AES::encrypt($jsonData);
            $result = System_Lib_Utils::base64_url_encode($result);
            $this->_retMessage($result);
        }else{

            $this->_retMessage($ret);

        }

    }

    private function GetWeixinAccessToken($originalId)
    {
        $publicAccount = XP_BModel_PublicAccount::getPublicAccount("",$originalId);
        if(empty($publicAccount)) throw new Exception("PublicAccount not found,originalId:".$originalId);

        $accessToken=XP_Lib_Weixin::getAccessToken($publicAccount->id,$publicAccount->appId,$publicAccount->appSecret);
        return $accessToken;
    }

    public function beforeFilter($action)
    {
    }

    /**
     * @param $func = array('func1', 'func2' ...)
     */
    protected function chkRequest($funcs, $params)
    {
        $flag = true;
        foreach ($funcs as $func){
            if ($this->$func($params)){
                $flag = false;
                break;
            }
        }

        return $flag;
    }

    /**
     * 解密参数
     * @param $cdata  base64_encode(aes({uin:xx,....}))
     * @return array('uin'=>'xx', ...... );
     */
    protected function decodeParams($cdata)
    {
        $cdata = System_Lib_Utils::base64_url_decode($cdata);
        $data = System_Lib_AES::decrypt($cdata);
        $params = json_decode($data,true);

        return $params;
    }

    /**
     * 判断是否发送过
     */
    protected function isSended($flowId)
    {
        // @todo 判断是否发送过
        $dao = WTuan_Model_WeixinPushed::dataAccess();
        $dao->filterByOp(WTuan_Model_WeixinPushed::FLOW_ID, '=', $flowId);
        $count = $dao->count();
        return $count > 0 ? true: false;
    }

    /**
     * 判断 请求时间是否超时
     * @param $time 请求时间 int
     */
    protected function isTimeout($params){
        $flag = false;
        if (!isset($params['tm']) || (time() - intval($params['tm'])) > 3600)
        {
            $flag = true;
        }
        return ;
    }

    protected function isClentErr($params)
    {
        $flag = false;
        if(!isset($params['ct']) || $params['ct'] != 6) {
            $flag = true;
        }
        return $flag;
    }

    protected function isClientversionErr($params)
    {
        $flag = false;
        if(!isset($params['v']) || $params['v'] != '2.0.0')
        {
            $flag = true;
        }
        return $flag;
    }

    protected function isParamsLost($params, $keys)
    {
        $flag = false;
        foreach($keys as $key){
            if (!isset($params[$key])) {
                $flag = true;
                break;
            }
        }
        return $flag;
    }

    /**
     * 错误信息处理
     */
    protected function _retMsgError($errorCode, $errorMsg = '', $params=array())
    {
        global $appStartTime;
        $data = array('retCode' => $errorCode,
            'retData' => array(),
            'msg' => MPApi_Lib_Error::getErrMsg($errorCode),
            'servertime' => time(),
            'usetime' => System_Lib_Utils::getExcuteTime($appStartTime),
        );
        MPApi_Lib_Log::debug('notify', 'RET_ERROR: ' . http_build_query($params).', error = '.MPApi_Lib_Error::getErrMsg($errorCode));
        $this->assignData('jsonData', json_encode($data));
        $this->render('DataJSON');
    }

    protected function _retMessage($retData, $params=array()){
        global $appStartTime;
        $data = array('retCode' => MPApi_Lib_Error::MPAPI_CODE_SUCC,
            'retData' => $retData,
            'msg' => '',
            'servertime' => time(),
            'usetime' => System_Lib_Utils::getExcuteTime($appStartTime),
        );
        MPApi_Lib_Log::debug('notify', 'RET_SUCCESS:'.http_build_query($params));
        $this->assignData('jsonData', json_encode($data));
        $this->render('DataJSON');
    }

    public function beforeRender($action)
    {
    }
}
