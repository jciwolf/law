<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-11-4
 * Time: 下午4:25
 * To change this template use File | Settings | File Templates.
 */

class XP_Controller_PublicAccount extends XP_Controller_Base
{

	protected $layoutName = 'XP_Layout_System';

	public function IndexAction()
	{
		$merchantId      = System_Lib_App::get('merchantId', System_Lib_Request::TYPE_INT, 1);
		$systemAccountId = System_Lib_App::get('systemAccountId', System_Lib_Request::TYPE_INT, 1);
		$wxconfig        = System_Lib_App::app()->getConfig("wxConfig");
		if (!empty($wxconfig)) {
			$this->assignData("wxurl", $wxconfig["url"]);
		}
		if (!empty($wxconfig)) {
			$this->assignData("wxtoken", $wxconfig["token"]);
		}
		$this->assignData("systemAccountId", $systemAccountId);
		$this->assignData("merchantId", $merchantId);
		$this->render("PublicAccount/Index");
	}

	public function AccIndexAction()
	{
		$this->render("PublicAccount/AccIndex");
	}

	public function AddOrUpdateAction()
	{
		$systemAccountId = System_Lib_App::get('systemAccountId', System_Lib_Request::TYPE_INT, 1);
		$id              = System_Lib_App::get('id', System_Lib_Request::TYPE_INT, 0);
		//var_dump($id);
		//var_dump($systemAccountId);
		$wxconfig = System_Lib_App::app()->getConfig("wxConfig");
		if (!empty($wxconfig)) {
			$this->assignData("wxurl", $wxconfig["url"]);
		}
		if (!empty($wxconfig)) {
			$this->assignData("wxtoken", $wxconfig["token"]);
		}
		if (!empty($id)) {
			$publicAccountModel = XP_BModel_PublicAccount::getPublicAccount($id);
			$this->assignData("publicAccountModel", $publicAccountModel);
		}
		$this->assignData("systemAccountId", $systemAccountId);
		$this->render("PublicAccount/AddOrUpdate");
	}

	public function TestAction()
	{
		$m            = new XP_BModel_PublicAccount();
		$m->name      = "我要票！";
		$m->id        = 1;
		$m->beginDate = date("Y-m-d H:i:s");
		//$r=	XP_BModel_PublicAccount::save($m);
		//var_dump($r);

		$n = array(
			'name'      => "I　am Zambia！",
			'beginDate' => date("Y-m-d H:i:s")
		);

		//$r = XP_BModel_PublicAccount::save($n);
		//var_dump($r);

		//XP_BModel_PublicAccount::delete(3);

		$a = XP_BModel_PublicAccount::get('', "");
		var_dump($a);

	}

}
