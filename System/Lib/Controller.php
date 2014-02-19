<?php

class System_Lib_Controller
{
	const DEFAULT_ACTION_FUNC_NAME_PERFIX = 'default';

	protected $path = null;
	protected $widgets = array();
	protected $data = array();
	protected $cacheKey = null;
	protected $interceptors = array();
	protected $internalInterceptors = array();

	/**
	 *
	 * @var System_Lib_Controller
	 */
	public $baseController = null;
	
	/**
	 *
	 * @var System_Lib_Controller
	 */
	private $_layout = null;
	protected $layoutName = null;

	/**
	 * 
	 */
	protected function defaultAction()
	{
		throw new Exception('controller must have defaultAction() function');
	}

	/**
	 *
	 * @return System_Lib_Layout
	 */
	public function layout()
	{
		if (is_null($this->_layout))
		{
			if (is_null($this->layoutName))
			{
				return null;
			}
			$this->_layout = new $this->layoutName;
		}
		return $this->_layout;
	}

	public function getInterceptors()
	{
		if (count($this->interceptors))
		{
			return $this->interceptors;
		}
		
		$className = get_class($this);
		$interceptors = array();
		while ($className)
		{
			$selfInterceptors = call_user_func(array($className, 'selfInterceptors'));
			if (count($selfInterceptors))
			{
				$interceptors = array_merge($selfInterceptors, $interceptors);
			}
			$className = get_parent_class($className);
		}

		return $interceptors;
	}
	
	public static function selfInterceptors()
	{
		return array();
	}

	/**
	 *
	 * @param string $action
	 */
	public function run($action)
	{
		$interceptors = array();
		$skipLogic = false;
		foreach ($this->getInterceptors() as $in => $acts)
		{
			if (empty($acts) || in_array($action, $acts))
			{
				$interceptor = new $in();
				$this->internalInterceptors[$in] = $interceptor;
				if ($interceptor->before($action) === false)
				{
					$skipLogic = true;
				}
				$interceptors[] = $interceptor;
			}
		}

		if (!$skipLogic)
		{
			$action = $action . 'Action';

			//beforeFilter
			if (is_callable(array($this, 'beforeFilter')))
			{
				$this->beforeFilter($action);
			}

			if (!is_null($this->layout()))
			{
				$this->widgetBegin($this->layout());
			}

			//beforeRender
			if (is_callable(array($this, 'beforeRender')))
			{
				$this->beforeRender($action);
			}

			if (is_callable(array($this, $action)))
			{
				$this->$action();
			}
			else
			{
				$func_name = self::DEFAULT_ACTION_FUNC_NAME_PERFIX . 'Action';
				if (is_callable(array($this, $action)))
				{
					$this->$func_name();
				}
				else
				{
					throw new Exception('controller must have '.$action.'() or '.$func_name.' function');
				}
			}

			if (!is_null($this->layout()))
			{
				$this->widgetEnd();
			}
		}

		foreach ($interceptors as $interceptor)
		{
			$interceptor->after();
		}
	}

	/**
	 *
	 * @param <string> $view
	 * @param <array> $data
	 */
	public function render($view, $data = array())
	{
		$this->data = array_merge($data, $this->data);
		$file_path = "{$this->getViewPath()}{$view}.php";
		echo $this->renderInternal($file_path);
	}

	/**
	 *
	 * @return <string>
	 */
	protected function getViewPath()
	{
		return PROJECT_PATH . "{$this->getPath()}/View/";
	}

	/**
	 *
	 * @return <string>
	 */
	protected function getPath()
	{
		if (!is_null($this->path))
		{
			return $this->path;
		}
		$this->path = array_shift(explode('_', get_class($this), 2));
		return $this->path;
	}

	/**
	 *
	 * @param <string> $filePath
	 * @return <string>
	 */
	protected function renderInternal($filePath)
	{
		extract($this->data, EXTR_PREFIX_SAME, 'data');
		ob_start();
		ob_implicit_flush(false);
		require($filePath);
		return trim(ob_get_clean());
	}

	/**
	 *
	 * @param <string> $name
	 * @param <mix> $value
	 */
	protected function assignData($name, $value)
	{
		$this->data[$name] = $value;
	}
	
	/**
	 *
	 * @param <string> $name
	 * @return <mix>
	 */
	protected function getData($name)
	{
		return isset($this->data[$name]) ? $this->data[$name] : null;
	}

	/**
	 *
	 * @param System_Lib_Widget $widget
	 */
	public function widget($widget)
	{
		$widget->baseController = $this->baseController;
		$widget->processOutput();
	}

	/**
	 *
	 * @param System_Lib_Widget $widget
	 */
	public function widgetBegin($widget)
	{
		$widget->baseController = $this->baseController;
		$this->widgets[] = $widget;
		$widget->init();
	}

	/**
	 * 
	 */
	public function widgetEnd()
	{
		$widget = array_pop($this->widgets);
		$widget->run();
	}

}