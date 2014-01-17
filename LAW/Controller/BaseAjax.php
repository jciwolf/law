<?php
/**
 * @author: deliliu
 * Date: 12-9-23
 * Time: 下午2:04
 */
class XP_Controller_BaseAjax extends System_Lib_Controller
{
	//protected $layoutName = null;
	protected $userId;
	public function beforeFilter()
	{
		if(!empty($_SESSION['id']))
		{
			$this->userId=$_SESSION['id'];
		}
		else
		{
			$this->renderJSON(10001,"","验证错误");
		}
	}

	public function renderJSON($retCode, $retData = '', $retMsg = '')
	{
		$data = array(
			'retCode' => $retCode,
			'retData' => $retData,
			'retMsg'  => $retMsg,
		);
		$this->assignData('retData', json_encode($data));
		$this->render('Ajax');
	}

	public function retError($retCode, $retMsg = '')
	{
		if (!$retMsg) {
			$retMsg = MP_Lib_Error::getErrMsg($retCode);
			if (!$retMsg) {
				$retMsg = '服务器超时，请重新再试';
			}
		}

		$this->renderJSON($retCode, '', $retMsg);
	}

	public function renderPlain($plain)
	{
		$this->assignData('retData', $plain);
		$this->render('Ajax');
	}

}
