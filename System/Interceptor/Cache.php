<?php
class System_Interceptor_Cache extends System_Lib_Interceptor
{
	protected $cache = null;
	protected $cacheHandler = null;
	protected $expire = 0;

	public function before(&$action)
	{
		$this->cache = new $this->cacheHandler();
		return !$this->cache->tryGet($this->getKey(), $this->expire);
	}

	public function after()
	{
		$this->cache->end();
	}

	protected function getKey()
	{
		throw new Exception('System_Interceptor_Cache::getKey() must be override.');
	}
}