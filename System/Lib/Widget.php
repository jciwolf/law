<?php

class System_Lib_Widget extends System_Lib_Controller
{

	/**
	 *
	 * @param <array> $data
	 */
	public function __construct($data = array())
	{
		$this->data = $data;
	}

	/**
	 * 
	 */
	protected function defaultAction()
	{
	}

	/**
	 * 
	 */
	public function init()
	{
		ob_start();
		ob_implicit_flush(false);
	}

	/**
	 * 
	 */
	public function run()
	{
		$this->assignData('content', ob_get_clean());
		$this->processOutput();
	}

	/**
	 * 
	 */
	public function processOutput()
	{
		$this->defaultAction();
		$this->render(array_pop(explode('_', get_class($this))));
	}
	
	/**
	 * 
	 */
	public function getContent()
	{
		$this->init();
		$this->processOutput();
		return trim(ob_get_clean());
	}

	/**
	 *
	 * @return <string>
	 */
	protected function getViewPath()
	{
		return PROJECT_PATH . "{$this->getPath()}/View/Widget/";
	}
}