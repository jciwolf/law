<?php
include_once('basic.php');

final class System_Lib_App
{
	/**
	 *
	 * @var System_Lib_Factory
	 */
	public $factory = null;
	
	/**
	 *
	 * @var <array>
	 */
	protected $config;

	/**
	 *
	 * @var System_Lib_App
	 */
	public static $app = null;

	/**
	 *
	 * @param <array> $config
	 * @return System_Lib_App
	 */
	public static function createApp($config)
	{
		//如果web server是php web server
		if (defined('IS_PWS'))
		{
			if (!isset($_PWS['app']))
			{
				$_PWS['app'] = new System_Lib_App($config);
			}
	
			return $_PWS['app'];
		}
		else
		{
			if (is_null(self::$app))
			{
				self::$app = new System_Lib_App($config);
			}
	
			return self::$app;
		}
	}	
	/**
	 *
	 * @return System_Lib_App
	 */
	public static function app()
	{
		//如果web server是php web server
		if (defined('IS_PWS'))
		{		
			return $_PWS['app'];
		}
		else
		{
			return self::$app;
		}

	}

	/**
	 *
	 * @param <array> $config
	 */
	public function __construct($config)
	{
		$this->config = $config;
	}

	/**
	 * 
	 */
	public function run()
	{
		System_Lib_App::app()->recordRunTime("\n+----------------");
		System_Lib_App::app()->recordRunTime('run begin '.$_SERVER['REQUEST_URI']);
		$route = $this->url()->parse();
		System_Lib_App::app()->request()->init();
		$controller = new $route['controller'];
		$controller->baseController = $controller;
		$controller->run($route['action']);
		$this->end();
	}

	/**
	 * 记录执行时间
	 * @param int $iStep 执行序号 < 100 为系统所用，>=100 为页面所用
	 */
	public function recordRunTime($log = '')
	{
		global $appStartTime;
		$appEndTime = microtime(true);
		$time = number_format($appEndTime - $appStartTime, 3, '.', '');
		$GLOBALS['run_times'][] = '['.date('H:i:s').'] '.$time.' '.($log != '' ? ' '.$log : '');
	}

	/**
	 *
	 * @return System_Lib_Request
	 */
	public function request()
	{
		return $this->factory()->get('System_Lib_Request');
	}

	/**
	 *
	 * @param <string> $url
	 * @param <bool> $permanent
	 */
	public static function redirect($url, $permanent = false)
	{
		System_Lib_App::app()->recordRunTime('redirect '.$url);
		System_Lib_App::app()->response()->redirect($url, $permanent);
	}
	
	/**
	 *
	 * @param <string> $name
	 * @param <int> $type
	 * @param <mix> $default_value
	 * @return <mix>
	 */
	public static function get($name = null, $type = System_Lib_Request::TYPE_INT, $defaultValue = null)
	{
		return System_Lib_App::app()->request()->get($name, $type, $defaultValue);
	}

	/**
	 *
	 * @param <string> $name
	 * @param <int> $type
	 * @param <mix> $default_value
	 * @return <mix>
	 */
	public static function getPost($name = null, $type = System_Lib_Request::TYPE_INT, $defaultValue = null)
	{
		return System_Lib_App::app()->request()->getPost($name, $type, $defaultValue);
	}

	/**
	 *
	 * @param <string> $name
	 * @param <int> $type
	 * @param <mix> $default_value
	 * @return <mix>
	 */
	public static function getRequest($name = null, $type = System_Lib_Request::TYPE_INT, $defaultValue = null)
	{
		return System_Lib_App::app()->request()->getRequest($name, $type, $defaultValue);
	}

	/**
	 *
	 * @param <string> $name
	 * @param <int> $type
	 * @param <mix> $default_value
	 * @return <mix>
	 */
	public static function getCookie($name = null, $type = System_Lib_Request::TYPE_INT, $defaultValue = null)
	{
		return System_Lib_App::app()->request()->getCookie($name, $type, $defaultValue);
	}
	
	public static function setCookie($name, $value, $expire = 0, $path = '/', $domain = null, $secure = false, $httponly = false)
	{
		return System_Lib_App::app()->response()->setCookie($name, $value, $expire, $path, $domain, $secure, $httponly);
	}
	
	public static function delCookie($name, $expire = 0, $path = '/', $domain = null, $secure = false, $httponly = false)
	{
		return System_Lib_App::app()->response()->delCookie($name, $expire, $path, $domain, $secure, $httponly);
	}
	
	/**
	 *
	 * @param <string> $controller
	 * @param <string> $action
	 * @param <array> $params
	 * @return <mix>
	 */
	public static function createUrl($controller, $action, $params = array())
	{
		return System_Lib_App::app()->url()->createUrl($controller, $action, $params);
	}

	/**
	 *
	 * @return System_Lib_Response
	 */
	public function response()
	{
		return $this->factory()->get('System_Lib_Response');
	}

	/**
	 *
	 * @return System_Lib_UrlManager
	 */
	public function url()
	{
		return $this->factory()->get('System_Lib_UrlManager');
	}
	
	/**
	 *
	 * @return System_Lib_PdoFactory
	 */
	public function pdo()
	{
		return $this->factory()->get('System_Lib_PdoFactory');
	}

	/**
	 *
	 * @param <string> $name
	 * @return <mix>
	 */
	public function getConfig($name = null)
	{
		if (is_null($name))
		{
			return $this->config;
		}
		return isset($this->config[$name]) ? $this->config[$name] : null;
	}

	/**
	 *
	 * @return System_Lib_Factory
	 */
	public function factory()
	{
		if (is_null($this->factory))
		{
			$this->factory = new System_Lib_Factory();
		}
		return $this->factory;
	}

	/**
	 * 
	 */
	public function end()
	{
		if (!defined('IS_PWS'))
		{
			global $appStartTime;
			$appEndTime = microtime(true);
			$time = number_format($appEndTime - $appStartTime, 3, '.', '');
			if ($time > 1) {
				if (!empty($GLOBALS['run_times'])) MF_Lib_Log::debug('runtime', join("\n", $GLOBALS['run_times']), false);
			}
			if (!empty($GLOBALS['sql_logs'])) MF_Lib_Log::sql($GLOBALS['sql_logs']);
			exit;
		}
	}

}