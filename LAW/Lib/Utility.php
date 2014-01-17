<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-10-30
 * Time: 下午1:42
 * To change this template use File | Settings | File Templates.
 */

class XP_Lib_Utility {

    private  static $connectTimeout = 30;
    private static $logSize = 5000000;
    /**
     * URL请求
     * @param string $url URL地址
     * @param string $body 发送的内容
     */
    public static function urlRequest($url, $data = '',$jsondecode=true)
    {
        $resp           = '';
        $wxapiStartTime = microtime(true);
        $httpParam      = array(
            //CURLOPT_SSL_VERIFYPEER	=> false,
            //CURLOPT_SSL_VERIFYHOST	=> false,
            //CURLOPT_USERAGENT		=> 'Weituangou Client',
        );
        if (!empty($data)) {
            $httpParam[CURLOPT_POST] = true;
            $httpParam[CURLOPT_POSTFIELDS] = $jsondecode?json_decode($data,true):$data;
        }
        $url=strpos($url,'http')===0 ? $url:'http://'.$_SERVER['HTTP_HOST'].$url;
        $return = System_Lib_ServerAccessor::CallCURL($url, $httpParam, $resp, array(), self::$connectTimeout);
        switch ($return) {
            case  0:
                $result = 'ok';
                break;
            case -1:
                $result = 'connect error';
                break;
            case -2:
                $result = 'responseCode not 200';
                break;
        }
        self::log("SpendTime:".System_Lib_Utils::getExcuteTime($wxapiStartTime)."\nUrl:".$url."\nData:".$data."\nResult:".$return."(".$result.")\nResponse:".$resp,"Utility");
        if ($return === 0) {
            $json = json_decode($resp, true);
        } else {
            $json=$return;
        }
        return $json;
    }
    /**
     * 记录日志
     * @param string $logMessage 日志信息
     * @param string module :模块
     */
    public static function log($logMessage,$module='')
    {
        if(!LOG_ENABLE) return;

        // 检查日志目录是否可写
        if ( !file_exists(LOG_PATH) ) {
            @mkdir(LOG_PATH);
        }
        @chmod(LOG_PATH,0777);
        if (!is_writable(LOG_PATH)) exit('LOG_PATH is not writeable !');
        $s_now_time=date("H:i:s") .".".floor(microtime()*1000);
        $micro=date("Y-m-d H:i:s") .".".floor(microtime()*1000);
        $log_now_day  = date('Y_m_d');
        $log_path   = LOG_PATH.'/Log_Xp_' . $log_now_day . '.log';
        if (file_exists($log_path) && self::$logSize <= filesize($log_path)) {
            $s_file_name = substr(basename($log_path), 0, strrpos(basename($log_path), '.log')). '_' . $s_now_time . '.log';
            rename($log_path, dirname($log_path) . '/' . $s_file_name);
        }
        clearstatcache();
        $pattern="/(\n)(\S*?)(\:)/im";
        $logMessage="\n".$logMessage;
        preg_match_all($pattern,$logMessage, $matches);
        for($i=0;$i<count($matches[0]);$i++){
            $logMessage=str_replace($matches[0][$i],$matches[1][$i].(strlen($matches[2][$i])>=10?$matches[2][$i]:$matches[2][$i].str_repeat(" ",10-strlen($matches[2][$i]))).$matches[3][$i]." ",$logMessage);
        }
        file_put_contents($log_path,"\n\nModule    : ".$module."\nTime      : ".$micro."\nContent   : /*--------------------------------------------------------------------------------".$logMessage."\n            --------------------------------------------------------------------------------*/ ", FILE_APPEND);
    }
    public static function contentFix($content) {
        $r=$result=null;
        $image=System_Lib_App::app()->getConfig('attachment');
        $arr=json_decode($content,true);
        if(is_array($arr)) {

		 if(isset($arr['file'])||isset($arr['mediaId']))
			{

				$result=array();
				$result["type"]=$arr['type'];
				$result["mediaId"]=$arr['mediaId'];
				$r=json_encode($result);
			}
			else
			{
            $result=array();
				foreach($arr as $item) {
					$item['PicUrl']=$image['host'].$image['path'].$item['PicUrl'];
					$result[]=$item;
				}
				$r=json_encode($result);
			}
        }

        else
            $r=str_replace('<br>',"\n",$content);

		XP_Lib_Utility::log("input:".$content."out:".json_encode($r),"XP_LIB_Utility::contentFix");
        return $r;
    }

    public static function jsonResult($errcode,$errmsg,$data) {
        $result=array();
        $result["errcode"]=$errcode;
        $result["errmsg"]=$errmsg;
        $result["data"]=$data;
        return json_encode($result);
    }
}