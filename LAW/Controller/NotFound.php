<?php

class XP_Controller_NotFound extends System_Lib_Controller
{
	protected $layoutName = 'XP_Layout_System';

    public function defaultAction()
	{
		$errCode = System_Lib_App::get("code", System_Lib_Request::TYPE_INT);
		$this->assignData('errorMsg', XP_Lib_Error::getErrMsg($errCode));
        $this->render('Err404');
    }

}
