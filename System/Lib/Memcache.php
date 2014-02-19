<?php
/**
 * memcache封装
 * 
 * @author anakinli
 * 
 * 2012-09-16
 */
class System_Lib_Memcache
{
	static $memcacheHash;
	
	/**
	 * @param $key 
	 * @param $server
	 * 
	 * @return 
	 */
	public static function Get($key, $server)
	{	
		if(!defined(CACHE_ENABLE) && CACHE_ENABLE === false )
		{
			return false;
		}
		
		$name = $server['ip'].'-'.$server['port'];
		
		if(!isset(self::$memcacheHash[$name]) || self::$memcacheHash[$name] === null)
		{
			self::$memcacheHash[$name] = new memcache();					
		}
		
		$memcache = self::$memcacheHash[$name];

		$memcache->addserver($server['ip'], $server['port'], true);

		$ret = $memcache->get($key);	
		
		return $ret;
	}
	/**
	 * @param $key 
	 * @param $server
	 * 
	 * @return 
	 */
	public static function Delete($key, $server)
	{	
		if(!defined(CACHE_ENABLE) && CACHE_ENABLE === false )
		{
			return false;
		}
		
		$name = $server['ip'].'-'.$server['port'];
		
		if(!isset(self::$memcacheHash[$name]) || self::$memcacheHash[$name] === null)
		{
			self::$memcacheHash[$name] = new memcache();					
		}
		
		$memcache = self::$memcacheHash[$name];

		$memcache->addserver($server['ip'], $server['port'], true);

		$ret = $memcache->delete($key);	
		
		return $ret;
	}

	/**
	 * @param $key
	 * @param $value
	 * @param $server
	 *
	 * @return
	 */
	public static function Set($key, $value, $ttl, $server)
	{
		if(!defined(CACHE_ENABLE) && CACHE_ENABLE === false )
		{
			return false;
		}

		$name = $server['ip'].'-'.$server['port'];

		if(!isset(self::$memcacheHash[$name]) || self::$memcacheHash[$name] === null)
		{
			self::$memcacheHash[$name] = new memcache();
		}

		$memcache = self::$memcacheHash[$name];
		$memcache->addserver($server['ip'], $server['port'], true);

		$ret = $memcache->set($key, $value, MEMCACHE_COMPRESSED, $ttl);

		return $ret;

	}


	/**
	 * 清除所有缓存
	 * @param $server
	 *
	 * @return bool
	 */
	public static function flush($server)
	{
		$name = $server['ip'].'-'.$server['port'];
		if(!isset(self::$memcacheHash[$name]) || is_null(sself::$memcacheHash[$name]))
		{
			self::$memcacheHash[$name] = new memcache();
		}

		$memcache = self::$memcacheHash[$name];
		$memcache->addserver($server['ip'], $server['port'], true);

		return $memcache->flush();
	}
}