<?php

class System_Lib_SlowLogMonitor
{
	private $msg;
	private $startTime;
	private $endTime;

	const SLOW_LIMIT = 0.2;	// 超时200ms则记日志
	
	public function __construct($msg = '')
	{
		$this->msg = $msg;
	}
	
	public function start($startTime = null)
	{
		$this->startTime = is_null($startTime) ? microtime(true) : $startTime;
	}
	
	public function end($endTime = null)
	{
		$this->endTime = is_null($endTime) ? microtime(true) : $endTime;
		$this->log();
	}
	
	private function log()
	{
		$cost = $this->endTime - $this->startTime;
		if ($cost >= self::SLOW_LIMIT)
		{
			System_Lib_Log::NetworkLog(System_Lib_Log::getAppLogId(), System_Lib_Log::NETWORKLOG_LOG_ERROR, 
							"{$this->msg} SlowLog::Cost {$cost}s");
		}
	}
}
