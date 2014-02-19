<?php
class System_Lib_Layout extends System_Lib_Widget
{
	/**
	 *
	 * @return <string>
	 */
	protected function getViewPath()
	{
		return PROJECT_PATH . "{$this->getPath()}/View/Layout/";
	}
}