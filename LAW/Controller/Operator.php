<?php
/**
 * @DESCRIPTION
 * 
 * 
 * @MODIFY
 * 13-11-12 下午3:47 create file
 * 
 * @author Kevin.Hu, Inc. <huwenhua@group-net.cn>
 * @version v2.1
 */

class XP_Controller_Operator extends XP_Controller_Base
{
	protected $layout = "XP_Layout_System";

	public function indexAction()
	{
		$distributorId=0;
		$distributorList=array();
		//var_dump($this->userId);
		if(!empty($this->userId))
		{
			$userModel=	XP_BModel_SystemAccount::getOne($this->userId);
			//var_dump($userModel);
			if(empty($userModel))
			{
				return;
			}
			if($userModel->type==XP_BModel_SystemAccount::TYPE_GAOPENG)
			{
				$distributorList=XP_BModel_Distributor::get('','',0,0,0,1000,1);
			}
			else if($userModel->type==XP_BModel_SystemAccount::TYPE_DISTRIBUTOR)
			{
				$distributorList[]=$userModel;
				$distributorId=$this->userId;
			}


		}

		//var_dump($distributorList);
		$this->assignData("distributorId", $distributorId);
		$this->assignData("distributorList", $distributorList);
		$this->render('Operator/Index');
	}
}