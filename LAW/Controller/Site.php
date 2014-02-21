<?php

class LAW_Controller_Site extends System_Lib_Controller
{
    protected $layoutName = 'LAW_Layout_Default';

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
