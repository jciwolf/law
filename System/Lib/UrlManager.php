<?php

class System_Lib_UrlManager
{
	const ROUTE_MAPPING = 'routeMapping';

	/**
	 *
	 * @var <array>
	 */
	protected $paramKeys = array();
	
	/**
	 *
	 * @var <mix>
	 */
	protected $paramKeyTags = null;


	protected function reset()
	{
		$this->paramKeyTags = null;
	}

	/**
	 *
	 * @param <string> $uri
	 * @return <array>
	 */
	public function parse($uri = null)
	{
		if (is_null($uri))
		{
			$uri = System_Lib_App::app()->request()->getUri();
		}
		$rs = $this->normailize($this->mappingToRoute($uri));
		$this->reset();
		return $rs;
	}

	/**
	 *
	 * @param <string> $controller
	 * @param <string> $action
	 * @param <array> $params
	 * @return string
	 */
	public function createUrl($controller, $action = Controller::DEFAULT_ACTION_FUNC_NAME_PERFIX, $params = array())
	{
		$route = $controller . '/' . $action;
		$pattern = rtrim(ltrim($this->mappingToPattern($route), '^'), '$');
		if (!$pattern)
			throw new Exception('route("' . $route . '") not exist, check your config.');
		$keys = $this->getParamKeys($pattern);
		if (count($keys))
		{
			$values = array();
			foreach ($keys as $key)
			{
				if (!isset($params[$key]))
					throw new Exception($key . ' must be set when creating this url');
				$values[] = $params[$key];
				unset($params[$key]);
			}
			$pattern = str_replace($this->getParamKeyTags($pattern), $values, $pattern);
		}
		if (count($params))
			$pattern .= '?' . http_build_query($params);

		$this->reset();
		return $pattern;
	}

	/**
	 *
	 * @param <string> $pattern
	 * @return <string>
	 */
	protected function buildPattern($pattern)
	{
		$pattern = preg_replace(array('/\//', '/<.+?>/'), array('\/', '([^\/]+)'), $pattern);
		return $pattern;
	}

	/**
	 *
	 * @param <string> $pattern
	 * @return <array>
	 */
	protected function getParamKeys($pattern)
	{
		return array_map(create_function('$str', 'return trim($str, \'<>\');'), $this->getParamKeyTags($pattern));
	}

	/**
	 *
	 * @param <string> $pattern
	 * @return <array>
	 */
	protected function getParamKeyTags($pattern)
	{
		if (is_null($this->paramKeyTags))
		{
			$this->paramKeyTags = array();
			if (preg_match_all('/<.+?>/', $pattern, $matches))
			{
				$this->paramKeyTags = $matches[0];
			}
		}
		return $this->paramKeyTags;
	}

	/**
	 *
	 * @param <string> $uri
	 * @return <string>
	 */
	protected function mappingToRoute($uri)
	{
		$uriPath = array_shift(explode('?', $uri, 2));
		foreach (System_Lib_App::app()->getConfig(self::ROUTE_MAPPING) as $pattern => $route)
		{
			if (preg_match('/^' . $this->buildPattern($pattern) . '/', $uriPath, $matches))
			{
				$this->paramKeys = $this->getParamKeys($pattern);
				$this->fixGetParams($matches);
				return $route;
			}
		}
		return false;
	}

	/**
	 *
	 * @param <string> $route
	 * @return <mix>
	 */
	protected function mappingToPattern($route)
	{
		$val = array_search($route, System_Lib_App::app()->getConfig(self::ROUTE_MAPPING));
		if (!$val)
		{
			throw new Exception('config ' . $route . ' routeMapping first.');
		}
		return $val;
	}

	/**
	 *
	 * @param <array> $params
	 */
	protected function fixGetParams($params)
	{
		if (count($this->paramKeys))
		{
			array_shift($params);
			$_GET = array_merge($_GET, array_combine($this->paramKeys, $params));
		}
	}

	/**
	 *
	 * @param <string> $route
	 * @return <array>
	 */
	protected function normailize($route)
	{
		if ($route)
		{
			list($controller, $action) = explode('/', $route);
			$route = array(
				'controller' => $controller,
				'action' => $action,
			);
		}
		else
		{
			$con = System_Lib_App::app()->getConfig('404Controller');
			$route = array(
				'controller' => is_null($con) ? 'System_Controller_NotFound' : $con,
				'action' => System_Lib_Controller::DEFAULT_ACTION_FUNC_NAME_PERFIX,
			);
		}
		return $route;
	}

}