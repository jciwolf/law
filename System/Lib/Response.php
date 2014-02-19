<?php

class System_Lib_Response
{
	/**
	 *
	 * @param <string> $url
	 * @param <bool> $permanent
	 */
	public function redirect($url, $permanent = false)
	{
		header("Location: $url", true, $permanent ? 301 : 302);
		System_Lib_App::app()->end();
	}

	/**
	 *
	 * @param <string> $name
	 * @param <string> $value
	 * @param <int> $expire
	 * @param <string> $path
	 * @param <string> $domain
	 * @param <bool> $secure
	 * @param <bool> $httponly
	 * @return <bool>
	 */
	public function setCookie($name, $value, $expire = 0, $path = '/', $domain = null, $secure = false, $httponly = false)
	{
		if (is_null($domain))
		{
			$domain = System_Lib_App::app()->request()->getHost(false);
		}
		System_Lib_App::app()->request()->setCookieGaeaLogic($name, $value);
		return setcookie($name, $value, $expire ? intval($expire) : 0, $path, $domain, $secure, $httponly);
	}
	
	/**
	 *
	 * @param <string> $name
	 * @param <int> $expire
	 * @param <string> $path
	 * @param <string> $domain
	 * @param <bool> $secure
	 * @param <bool> $httponly
	 * @return <bool>
	 */
	public function delCookie($name, $expire = 0, $path = '/', $domain = null, $secure = false, $httponly = false)
	{
		if (is_null($domain))
		{
			$domain = System_Lib_App::app()->request()->getHost(false);
		}
		System_Lib_App::app()->request()->delCookieGaeaLogic($name);
		return setcookie($name, '', $expire ? intval($expire) : 0, $path, $domain, $secure, $httponly);
	}

	/**
	 *
	 * @param <string> $name
	 * @param <string> $value
	 * @param <int> $http_reponse_code
	 */
	public function setHeader($name, $value, $http_reponse_code = null)
	{
		header("$name: $value", true, $http_reponse_code);
	}

	/**
	 *
	 * @param <string> $content_type
	 * @param <string> $charset
	 */
	public function setContentType($content_type, $charset = null)
	{
		if (!$charset && preg_match('/^text/i', $content_type))
		{
			$charset = System_Lib_App::app()->getConfig('charset');
			if (is_null($charset))
				$charset = 'utf-8';
		}
		if ($charset)
		{
			$this->setHeader("content-type", "$content_type; charset=$charset");
		}
		else
		{
			$this->setHeader("content-type", $content_type);
		}
	}

	/**
	 *
	 * @param <string> $value
	 */
	public function setCacheControl($value)
	{
		$this->setHeader("cache-control", $value);
	}
	
}