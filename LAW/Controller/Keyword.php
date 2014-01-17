<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jerry Qian (QianXufeng)
 * Date: 13-10-30
 * Time: 上午10:21
 * To change this template use File | Settings | File Templates.
 */

class XP_Controller_Keyword extends XP_Controller_BasePublic {
	protected $layoutName = 'XP_Layout_Public';

    public function indexAction()
    {
        $image=System_Lib_App::app()->getConfig('attachment');
        $PublicAccountId = System_Lib_App::getRequest("pid", System_Lib_Request::TYPE_INT, 0);
        $this->assignData("PublicAccountId", $PublicAccountId);
        $this->assignData("mediaHost", $image['path']);
        $this->render('Keyword/Index');
    }
    public function infoAction()
    {
        $image=System_Lib_App::app()->getConfig('attachment');
        $PublicAccountId = System_Lib_App::getRequest("pid", System_Lib_Request::TYPE_INT, 0);
        $id = System_Lib_App::getRequest("id", System_Lib_Request::TYPE_INT, 0);
        $this->assignData("PublicAccountId", $PublicAccountId);
        $this->assignData("Id", $id);
        $this->assignData("mediaHost", $image['path']);
        $this->render('Keyword/Info');
    }
}
?>