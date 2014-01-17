<?php

class XP_Controller_Site extends XP_Controller_Base
{
    protected $layoutName = 'XP_Layout_Default';

	public function logoutAction()
	{
		session_unset();
		session_destroy();
        unset($_COOKIE['LoginInfo']);
        setcookie('LoginInfo', null, -1, '/');

        $redirectUrl = "/login";
		System_Lib_App::app()->redirect($redirectUrl);
	}

    public function indexAction()
    {
		$this->render('Site/Index');
    }
}
