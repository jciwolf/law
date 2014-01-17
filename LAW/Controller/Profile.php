<?php
/**
 * @DESCRIPTION
 * 
 * 
 * @MODIFY
 * 13-11-12 下午4:51 create file
 * 
 * @author Kevin.Hu, Inc. <huwenhua@group-net.cn>
 * @version v2.1
 */

class XP_Controller_Profile extends XP_Controller_Base
{
	protected $layout = 'XP_Layout_System';

	public function indexAction()
	{
		$this->render('Profile/Index');
	}
}
