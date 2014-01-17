<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-3-18
 * Time: 上午9:42
 * To change this template use File | Settings | File Templates.
 */

class XP_Layout_Blank extends System_Lib_Layout
{
	private $jsArr = array();
	private $cssArr = array();

	public function defaultAction()
	{
		$this->assignData('cssArr', $this->cssArr);
		$this->assignData('jsArr', $this->jsArr);
	}

	public function addJs($path)
	{
		$this->jsArr[] = $path;
	}

	public function addCss($path)
	{
		$this->cssArr[] = $path;
	}

}