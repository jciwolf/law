<?php

class System_Lib_Request
{

	const TYPE_INT = 0;
	const TYPE_STRING = 1;

	/**
	 *
	 * @var <string>
	 */
	private $_requestUri;

	/**
	 *
	 * @var <string>
	 */
	private $_url;
	private $_get;
	private $_post;
	private $_cookie;
	private $_isInited = false;


	public function init()
	{
		if (!$this->_isInited)
		{
			$this->_get = $_GET;
			$this->_post = $_POST;
			$this->_cookie = $_COOKIE;
			//unset($_GET);
			//unset($_POST);
			$this->_isInited = true;
		}
	}

	/**
	 *
	 * @param <type> $array
	 * @param <string> $name
	 * @param <int> $type
	 * @param <mix> $default_value
	 * @return <mix>
	 */
	private function getInternal($array, $name, $type = self::TYPE_INT, $defaultValue = '')
	{
		if (is_null($name))
		{
			if (get_magic_quotes_gpc())
			{
				array_walk_recursive($array, create_function('&$val, $key', '$val = stripslashes($val);'));
			}
			return $array;
		}
		if (!isset($array[$name]))
		{
			return $defaultValue;
		}
		$value = get_magic_quotes_gpc() ? stripslashes($array[$name]) : $array[$name];
		if ($type == self::TYPE_INT)
		{
			return intval($value);
		}
		return strval($value);
	}

	/**
	 *
	 * @param <string> $name
	 * @param <int> $type
	 * @param <mix> $default_value
	 * @return <mix>
	 */
	public function get($name = null, $type = self::TYPE_INT, $defaultValue = null)
	{
		return $this->getInternal($this->_get, $name, $type, $defaultValue);
	}

	/**
	 *
	 * @param <string> $name
	 * @param <int> $type
	 * @param <mix> $default_value
	 * @return <mix>
	 */
	public function getPost($name = null, $type = self::TYPE_INT, $defaultValue = null)
	{
		return $this->getInternal($this->_post, $name, $type, $defaultValue);
	}

	/**
	 *
	 * @param <string> $name
	 * @param <int> $type
	 * @param <mix> $default_value
	 * @return <mix>
	 */
	public function getRequest($name = null, $type = self::TYPE_INT, $defaultValue = null)
	{
		return $this->getInternal(array_merge($this->_get, $this->_post), $name, $type, $defaultValue);
	}

	/**
	 *
	 * @param <string> $name
	 * @param <int> $type
	 * @param <mix> $default_value
	 * @return <mix>
	 */
	public function getCookie($name = null, $type = self::TYPE_INT, $defaultValue = null)
	{
		return $this->getInternal($this->_cookie, $name, $type, $defaultValue);
	}

	/**
	 *
	 * @param <string> $name
	 * @param <int> $type
	 * @param <mix> $default_value
	 * @return <mix>
	 */
	public function getFile($name = null, $defaultValue = null)
	{
		if (is_null($name))
		{
			return $_FILES;
		}
		return isset($_FILES[$name]) ? $_FILES[$name] : $defaultValue;
	}

	/**
	 *
	 * @return <string>
	 */
	public function getUrl()
	{
		if (is_null($this->_url))
		{
			$this->_url = $this->getHost() . $this->getUri();
		}
		return $this->_url;
	}

	/**
	 *
	 * @return <string>
	 */
	public function getHost($prefix = true)
	{
		$host = $_SERVER['HTTP_HOST'] != '' ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
		return ($prefix ? 'http://' : '') . array_shift(explode(':', $host, 2));
	}

	/**
	 *
	 * @return <sting>
	 */
	public function getAppName()
	{
		return basename(dirname($_SERVER['DOCUMENT_ROOT']));
	}

	/**
	 *
	 * @return <string>
	 */
	public function getUri()
	{
		if (is_null($this->_requestUri))
		{
			if (isset($_SERVER['REQUEST_URI']))
			{	//如果web server是php web server
				if (defined('IS_PWS'))
				{
					$this->_requestUri = preg_replace("/pwsuri=/", '', preg_replace("/&/", '?', $_SERVER['QUERY_STRING'], 1), 1);
				}
				else
				{
					$this->_requestUri = $_SERVER['REQUEST_URI'];
				}
			}
		}
		return $this->_requestUri;
	}

	/**
	 *
	 * @return <string>
	 */
	public function getQueryString()
	{
		return isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
	}

	/**
	 *
	 * @return <string>
	 */
	public function getReferer()
	{
		return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
	}

	/**
	 *
	 * @return <string>
	 */
	public function getUserAgent()
	{
		return $_SERVER['HTTP_USER_AGENT'];
	}

	/**
	 *
	 * @return <string>
	 */
	public function getUserIp()
	{
		return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
	}

	public function setCookieGaeaLogic($name, $value)
	{
		$this->_cookie[$name] = $value;
	}

	public function delCookieGaeaLogic($name)
	{
		unset($this->_cookie[$name]);
	}
}