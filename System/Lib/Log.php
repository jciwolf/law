<?php

class System_Lib_Log
{
	//log level
	const NETWORKLOG_LOG_EMERG = 0;  /* system is unusable               */
	const NETWORKLOG_LOG_ALERT = 1;  /* action must be taken immediately */
	const NETWORKLOG_LOG_CRIT = 2;   /* critical conditions              *///关键点发生异常(CallServer超时，TTC超时)
	const NETWORKLOG_LOG_ERROR = 3;  /* error conditions                 *///错误日志(前台参数验证错误，调用Server返回错误)
	const NETWORKLOG_LOG_WARNING = 4;/* warning conditions               */
	const NETWORKLOG_LOG_NOTICE = 5; /* normal but significant condition *///统计相关日志
	const NETWORKLOG_LOG_INFO = 6;   /* informational                    *///流水日志
	const NETWORKLOG_LOG_DEBUG = 7;  /* debug-level messages             */
	const NETWORKLOG_LOG_TRACE = 8;  /* trace-level messages             */

	
	//统计来源
	const NETWORKLOG_LOG_SRC_DEFAULT = 0;

	public static function NetworkLog($appid, $level, $content, $src = self::NETWORKLOG_LOG_SRC_DEFAULT)
	{
		return true;
	}

	private static function UdpSend($strSend, $iSendLen, &$sErrMsg, $strAddress, $iTimeout = 1)
	{
	    return true;
	}
	
	public static function getAppLogId()
	{

		return 0;
	}
}