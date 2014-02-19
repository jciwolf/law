<?php
class System_Lib_Interceptor
{
	public function before(&$action)
	{
		throw new Exception('System_Lib_Interceptor::before() must be override.');
	}
	
	public function after()
	{
		throw new Exception('System_Lib_Interceptor::after() must be override.');
	}
}