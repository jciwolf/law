<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-11-4
 * Time: 下午4:25
 * To change this template use File | Settings | File Templates.
 */

class XP_Controller_Merchant extends XP_Controller_Base

{
	protected $layoutName = 'XP_Layout_System';

	public function IndexAction()
	{
		$distributorId = System_Lib_App::get('distributorId', System_Lib_Request::TYPE_INT, 1);
		$this->assignData("distributorId", $distributorId);
		$this->render('Merchant/Index');
	}

	public function AddOrUpdateAction()
	{
		$distributorId = System_Lib_App::get('distributorId', System_Lib_Request::TYPE_INT, 1);
		$id            = System_Lib_App::get('id', System_Lib_Request::TYPE_INT, 0);
		//var_dump($id);

		if (!empty($id)) {
			$publicAccountModel = XP_BModel_Merchant::getOne($id);
			$this->assignData("merchantModel", $publicAccountModel);
		}
		$this->assignData("distributorId", $distributorId);
		$this->assignData("merchantId", $id);
		$this->render("Merchant/AddOrUpdate");
	}

	public function TestAction()
	{
		$m            = new XP_BModel_Merchant();
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

		$a = XP_BModel_Merchant::getOne(2);
		var_dump($a);

	}

}
