<?php

class XP_Controller_Login extends System_Lib_Controller
{
    protected $layoutName = 'XP_Layout_Default';

    public function loginAction()
    {
        $redirectUrl=System_Lib_App::app()->getRequest('redirectUrl', System_Lib_Request::TYPE_STRING);
        if(empty($redirectUrl)) $redirectUrl='/';
        $this->assignData('redirectUrl',$redirectUrl);
        $this->render('Site/Login');
    }
}
