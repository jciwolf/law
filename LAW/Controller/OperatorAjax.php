<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-10-31
 * Time: 下午2:36
 * To change this template use File | Settings | File Templates.
 */

class XP_Controller_OperatorAjax extends XP_Controller_BaseAjax
{
	protected $layoutName = null;

	public function getAction()
	{
		//var_dump(1);
		$outPut       = "N";
		$distributor  = System_Lib_App::getPost('distributor', System_Lib_Request::TYPE_STRING, '');
		$email         = System_Lib_App::getPost('email', System_Lib_Request::TYPE_STRING, '');
		$beginDate    = System_Lib_App::getPost('beginDate', System_Lib_Request::TYPE_STRING, '');
		$endDate      = System_Lib_App::getPost('endDate', System_Lib_Request::TYPE_STRING, '');
		$type         = System_Lib_App::getPost('type', System_Lib_Request::TYPE_INT, 0);
		$status       = System_Lib_App::getPost('status', System_Lib_Request::TYPE_INT, 0);
		$rows         = System_Lib_App::getPost('rows', System_Lib_Request::TYPE_INT, 30);
		$page         = System_Lib_App::getPost('page', System_Lib_Request::TYPE_INT, 1);
		$errorCode    = 0;
		$errorMessage = '';
		//var_dump($distributor);
		if (!empty($rows)) {
			$r = XP_BModel_Operator::get('', $distributor, $beginDate, $endDate, $status, $rows, $page,	$this->userId,$email);
			if (!empty($r)) {
				$outPut = $r;
			}
		}
		echo XP_Lib_Utility::jsonResult($errorCode, $errorMessage, $outPut);
	}

	public function getOneAction()
	{
		//var_dump(1);
		$outPut       = "";
		$OperatorId   = System_Lib_App::getPost('operatorID', System_Lib_Request::TYPE_INT, 0);
		$errorCode    = 0;
		$errorMessage = '';
		//var_dump($a);
		if (!empty($OperatorId)) {
			$r = XP_BModel_Operator::getOne($OperatorId);
			if (!empty($r)) {
				$r->password="";
				$outPut = $r;
			}
		}
		echo XP_Lib_Utility::jsonResult($errorCode, $errorMessage, $outPut);
	}

	public function UpdateOrSaveAction()
	{
		$outPut       = "";
		$errorCode    = 0;
		$errorMessage = '';
		$filters      = array
		(
			//'parentId'=>FILTER_VALIDATE_INT,
			"name"      => array
			(
				"filter" => FILTER_SANITIZE_STRING
			),

			"email"     => FILTER_VALIDATE_EMAIL,
			"mobile"    => array(
				"filter"  => FILTER_VALIDATE_REGEXP,
				"options" => array("regexp" => "/^[0-9]{11}$/i")
			),
			"qq"        => array
			(
				"filter" => FILTER_SANITIZE_NUMBER_INT
			),
			"status"    => array
			(
				"filter"  => FILTER_VALIDATE_REGEXP,
				"options" => array("regexp" => "/^[1-2]{1}$/i")
			),

		);

		$result = filter_input_array(INPUT_POST, $filters);
		if (in_array(false, $result)) {
			$errorCode    = 20001;
			$errorMessage = "please input validate parameters";
			exit(XP_Lib_Utility::jsonResult($errorCode, $errorMessage, $outPut));
		}

		$model             = array();
		$model["parentId"] =1;// System_Lib_App::getPost('operatorId', System_Lib_Request::TYPE_INT, 0);
		$model["id"]       = System_Lib_App::getPost('operatorId', System_Lib_Request::TYPE_STRING, '');
		$model["mobile"]   = System_Lib_App::getPost('mobile', System_Lib_Request::TYPE_STRING, '');



		$model["password"] = System_Lib_App::getPost('password', System_Lib_Request::TYPE_STRING, '');
		$model["email"]    = System_Lib_App::getPost('email', System_Lib_Request::TYPE_STRING, '');
		$model["qq"]       = System_Lib_App::getPost('qq', System_Lib_Request::TYPE_STRING, '');
		$model["status"]   = System_Lib_App::getPost('status', System_Lib_Request::TYPE_INT, 0);
		$model["name"]     = System_Lib_App::getPost('name', System_Lib_Request::TYPE_STRING, 0);
		$model["parentId"]     = System_Lib_App::getPost('distributorId', System_Lib_Request::TYPE_INT, 0);
		if (empty($model["id"])) {
			$model["createTime"] = date("Y-m-d H:i:s");
		}
		$model["updateTime"] = date("Y-m-d H:i:s");
		$model["type"]       = 3;
		if(!empty($model["password"])){
			$secret=System_Lib_App::app()->getConfig('secret');
			$model["password"]=md5($model["password"]+$secret['password']);
		}
		$r                   = XP_BModel_Operator::save($model);
		if (!empty($r)) {
			$outPut = $r;
		}

		echo XP_Lib_Utility::jsonResult($errorCode, $errorMessage, $outPut);
	}

	public function UpdateStatusAction()
	{
		$outPut          = "";
		$errorCode       = 0;
		$errorMessage    = '';
		$model           = array();
		$model["id"]     = System_Lib_App::getPost('id', System_Lib_Request::TYPE_INT, 0);
		$model["status"] = System_Lib_App::getPost('status', System_Lib_Request::TYPE_INT, 0);
		if (empty($model["id"]) || empty($model["status"])) {
			return "N";
		}
		$model["updateTime"] = date("Y-m-d H:i:s");
		$r                   = XP_BModel_Operator::save($model);
		if (!empty($r)) {
			$outPut = $r;
		}

		echo XP_Lib_Utility::jsonResult($errorCode, $errorMessage, $outPut);
	}

	public function CheckEmailAction()
	{

		$errorCode    = 0;
		$errorMessage = '';
		$outPut       = '';
		$email        = System_Lib_App::getRequest('email', System_Lib_Request::TYPE_STRING);
		$id           = System_Lib_App::getRequest('mid', System_Lib_Request::TYPE_INT);
		if (!empty($email)) {
			$r = XP_BModel_Operator::checkOperatorEmailExist($email, $id);
			if (!empty($r)) {
				$outPut = true;
			} else {
				$outPut = false;

			}
		}
		echo XP_Lib_Utility::jsonResult($errorCode, $errorMessage, $outPut);
	}

	public function CheckNameAction()
	{
		$errorCode    = 0;
		$errorMessage = '';
		$outPut       = '';
		$name         = System_Lib_App::getRequest('name', System_Lib_Request::TYPE_STRING);
		$id           = System_Lib_App::getRequest('mid', System_Lib_Request::TYPE_INT);
		if (!empty($name)) {
			$r = XP_BModel_Operator::checkOperatorNameExist($name, $id);
			if (!empty($r)) {
				$outPut = true;
			} else {
				$outPut = false;

			}
		}
		echo XP_Lib_Utility::jsonResult($errorCode, $errorMessage, $outPut);

	}

	public function deleteAction()
	{
		$outPut       = "";
		$errorCode    = 0;
		$errorMessage = '';
		$id           = System_Lib_App::getPost('id', System_Lib_Request::TYPE_INT, 0);
		if (empty($id)) {
			$errorCode    = "2001";
			$errorMessage = 'id不能为空';
		}
		$r = XP_BModel_Operator::delete($id);
		if (!empty($r)) {
			$outPut = $r;
		}
		echo XP_Lib_Utility::jsonResult($errorCode, $errorMessage, $outPut);
	}

	public function batchDeleteAction()
	{
		$outPut       = "";
		$errorCode    = 0;
		$errorMessage = '';
		$ids          = System_Lib_App::getRequest('ids', System_Lib_Request::TYPE_STRING);
		$ids          = explode(",", $ids);
		if (is_array($ids)) {
			XP_BModel_Operator::batchDelete($ids);
		} else {
			$errorCode = "20001";
		}
		echo XP_Lib_Utility::jsonResult($errorCode, $errorMessage, $outPut);
	}

	public function batchUpdateStatusAction()
	{

		$outPut       = "";
		$errorCode    = 0;
		$errorMessage = '';
		$ids          = System_Lib_App::getRequest('ids', System_Lib_Request::TYPE_STRING);
		$status       = System_Lib_App::getRequest('status', System_Lib_Request::TYPE_INT);
		$ids          = explode(",", $ids);
		if (is_array($ids)) {
			if (XP_BModel_Operator::batchUpdateStatus($ids, $status, 0)) {

			} else {
				$errorCode = "23000";
			}
		}
		echo XP_Lib_Utility::jsonResult($errorCode, $errorMessage, $outPut);
	}

}

?>