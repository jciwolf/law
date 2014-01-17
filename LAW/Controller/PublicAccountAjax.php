<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-10-31
 * Time: 下午2:36
 * To change this template use File | Settings | File Templates.
 */

class XP_Controller_PublicAccountAjax extends XP_Controller_BaseAjax
{
	protected $layoutName = null;

	public function getAction()
	{
		$outPut            = "N";
		$PubicAccountId    = System_Lib_App::getPost('PubicAccountId', System_Lib_Request::TYPE_INT, 1);
		$systemAccountName = System_Lib_App::getPost('systemAccountName', System_Lib_Request::TYPE_STRING, '');
		$name              = System_Lib_App::getPost('name', System_Lib_Request::TYPE_STRING, '');
        $weixin            = System_Lib_App::getPost('weixin', System_Lib_Request::TYPE_STRING, '');
		$beginDate         = System_Lib_App::getPost('beginDate', System_Lib_Request::TYPE_STRING, '');
		$endDate           = System_Lib_App::getPost('endDate', System_Lib_Request::TYPE_STRING, '');
		$type              = System_Lib_App::getPost('type', System_Lib_Request::TYPE_INT, 0);
		$status            = System_Lib_App::getPost('status', System_Lib_Request::TYPE_INT, 0);
		$rows              = System_Lib_App::getPost('rows', System_Lib_Request::TYPE_INT, 30);
		$page              = System_Lib_App::getPost('page', System_Lib_Request::TYPE_INT, 1);

		//var_dump($PubicAccountId);
		$errorCode    = 0;
		$errorMessage = '';
		if (!empty($PubicAccountId)) {
			$r = XP_BModel_PublicAccount::get($systemAccountName, $name, $weixin, $beginDate, $endDate, $type, $status, $rows, $page);
			if (!empty($r)) {
				$outPut = $r;
			} else {
				$errorCode    = 20001;
				$errorMessage = '无法得到数据！';

			}
		}
		echo XP_Lib_Utility::jsonResult($errorCode, $errorMessage, $outPut);
	}

	public function getOneAction()
	{
		$outPut         = "N";
		$PubicAccountId = System_Lib_App::getPost('PubicAccountId', System_Lib_Request::TYPE_INT, 1);

		//var_dump($PubicAccountId);
		$errorCode    = 0;
		$errorMessage = '';
		if (!empty($PubicAccountId)) {

			$r = XP_BModel_PublicAccount::getPublicAccount($PubicAccountId);
			if (!empty($r)) {
				$outPut = $r;
			} else {
				$errorCode    = 20001;
				$errorMessage = '无法得到数据！';

			}
		}
		echo XP_Lib_Utility::jsonResult($errorCode, $errorMessage, $outPut);
	}

	public function UpdateOrSaveAction()
	{
		$outPut                   = "N";
		$errorCode                = 0;
		$errorMessage             = '';
		$model                    = array();
		$model["id"]              = System_Lib_App::getPost('id', System_Lib_Request::TYPE_INT, 0);
		$model["name"]            = System_Lib_App::getPost('name', System_Lib_Request::TYPE_STRING, '');
		$model["originalId"]      = System_Lib_App::getPost('originalId', System_Lib_Request::TYPE_STRING);
		$model["weixin"]          = System_Lib_App::getPost('weixin', System_Lib_Request::TYPE_STRING, '');
		$model["weixinPay"]       = System_Lib_App::getPost('weixinPay', System_Lib_Request::TYPE_STRING, '');
		$model["customerService"]       = System_Lib_App::getPost('customerService', System_Lib_Request::TYPE_INT, 0);
		$model["systemAccountId"] = System_Lib_App::getPost('systemAccountId', System_Lib_Request::TYPE_INT, 0);
		$model["beginDate"]       = System_Lib_App::getPost('beginDate', System_Lib_Request::TYPE_STRING, '');
		$model["endDate"]         = System_Lib_App::getPost('endDate', System_Lib_Request::TYPE_STRING, '');

		$model["token"]  = System_Lib_App::getPost('token', System_Lib_Request::TYPE_STRING, '');
		$model["note"]   = System_Lib_App::getPost('note', System_Lib_Request::TYPE_STRING, '');
		$model["status"] = System_Lib_App::getPost('status', System_Lib_Request::TYPE_INT, 0);
		$model["type"]   = System_Lib_App::getPost('type', System_Lib_Request::TYPE_INT, 0);
        $model["token"]  = System_Lib_App::getPost('token', System_Lib_Request::TYPE_STRING, '');
        $model["appId"]  = System_Lib_App::getPost('appId', System_Lib_Request::TYPE_STRING, '');
        $model["appSecret"]  = System_Lib_App::getPost('appSecret', System_Lib_Request::TYPE_STRING, '');

		$wxconfig = System_Lib_App::app()->getConfig("wxConfig");
		if (!empty($wxconfig)) {
			$token = $wxconfig["token"];
		}

		$model["url"] = $wxconfig["url"];
		// var_dump( $wxconfig["url"]);
		if (empty($model["id"])) {
			$model["createTime"] = date("Y-m-d H:i:s");
		}
		$model["updateTime"] = date("Y-m-d H:i:s");

		$r = XP_BModel_PublicAccount::save($model);
		if (!empty($r)) {
			if ($model["token"] == $token) {

			} else {
				if (!empty($model["id"])) {
					//var_dump($wxconfig["url"]."/"+$model["id"]);
					$model["url"] = $wxconfig["url"] . "/" . $model["id"];
				} else {
					$model["url"] = $wxconfig["url"] . "/" . $r;
					$model["id"]  = $r;
				}
				$model["updateTime"] = date("Y-m-d H:i:s");
				$r                   = XP_BModel_PublicAccount::save($model);
			}

			$outPut = $r;
		}

		echo XP_Lib_Utility::jsonResult($errorCode, $errorMessage, $outPut);
	}

	public function UpdateStatusAction()
	{
		$outPut          = "N";
		$errorCode       = 0;
		$errorMessage    = '';
		$model           = array();
		$model["id"]     = System_Lib_App::getPost('id', System_Lib_Request::TYPE_INT, 0);
		$model["status"] = System_Lib_App::getPost('status', System_Lib_Request::TYPE_INT, 0);
		if (empty($model["id"]) || empty($model["status"])) {
			return "N";
		}
		$model["updateTime"] = date("Y-m-d H:i:s");
		$r                   = XP_BModel_PublicAccount::save($model);
		if (!empty($r)) {
			$outPut = $r;
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
			$errorCode = 30001;
		} else {
			$r = XP_BModel_PublicAccount::delete($id);
			if (!empty($r)) {
				$outPut = $r;
			} else {
				$errorCode = "20001";
			}
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
			XP_BModel_PublicAccount::batchDelete($ids);
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
			if (XP_BModel_PublicAccount::batchUpdateStatus($ids, $status, 0)) {

			} else {
				$errorCode = "23000";
			}
		}
		echo XP_Lib_Utility::jsonResult($errorCode, $errorMessage, $outPut);
	}

	public function checkExistAction()
	{
		$errorCode    = 0;
		$errorMessage = '';
		$outPut       = '';
		$weixinName   = System_Lib_App::getRequest('weixinName', System_Lib_Request::TYPE_STRING);
		$id           = System_Lib_App::getRequest('id', System_Lib_Request::TYPE_INT);
		if (!empty($weixinName)) {
			$r = XP_BModel_PublicAccount::checkExist($weixinName, $id);
			if (!empty($r)) {
				$outPut = true;
			} else {
				$outPut = false;

			}
		}
		echo XP_Lib_Utility::jsonResult($errorCode, $errorMessage, $outPut);
	}

	public function checkOriginalIdAction()
	{
		$errorCode    = 0;
		$errorMessage = '';
		$outPut       = '';
		$OriginalId   = System_Lib_App::getRequest('OriginalId', System_Lib_Request::TYPE_STRING);
		$id           = System_Lib_App::getRequest('id', System_Lib_Request::TYPE_INT);
		if (!empty($OriginalId)) {
			$r = XP_BModel_PublicAccount::checkOriginalExist($OriginalId, $id);
			if (!empty($r)) {
				$outPut = true;
			} else {
				$outPut = false;

			}
		}
		echo XP_Lib_Utility::jsonResult($errorCode, $errorMessage, $outPut);
	}

	public function checkWeixinIdAction()
	{
		$errorCode    = 0;
		$errorMessage = '';
		$outPut       = '';
		$weixin       = System_Lib_App::getRequest('weixin', System_Lib_Request::TYPE_STRING);
		$id           = System_Lib_App::getRequest('id', System_Lib_Request::TYPE_INT);
		if (!empty($weixin)) {
			$r = XP_BModel_PublicAccount::checkWeixinExist($weixin, $id);
			if (!empty($r)) {
				$outPut = true;
			} else {
				$outPut = false;

			}
		}
		echo XP_Lib_Utility::jsonResult($errorCode, $errorMessage, $outPut);
	}

//活动内容文件添加
	public function uploaderAction()
	{

		// Define a destination

		//var_dump($_POST);
		//var_dump($_GET);
		$rootUrl      = System_Lib_App::app()->getConfig('mediaRoot');
		$targetFolder = $rootUrl . "/banner";

		if (!empty($_FILES)) {

			$date           = date("Y/m/d");
			$filenamePrefix = $date . "/" . time() . "_" . rand(100, 999);
			$tempFile       = $_FILES['Filedata']['tmp_name'];
			$ext            = strtolower(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION));
			$targetPath     = $targetFolder;
			//var_dump($ext);
			//var_dump($activityId);

			$newFileName = $filenamePrefix . "." . $ext;

			$targetFile = rtrim($targetPath, '/') . '/' . $newFileName;

			//move_uploaded_file($tempFile,$targetFile);

			if (System_Lib_File::copy($tempFile, $targetFile)) {
				//同步目录
				$syncCmd = '/data/soft/wmobile.rsync.sh';

				if (file_exists($syncCmd)) {
					$syncCmd .= ' ' . str_replace(System_Lib_App::app()->getConfig('mediaRoot'), '', dirname($targetFile));
					$str = System_Lib_Utils::system($syncCmd);
					//debug($str);
				}
			}
			$this->renderPlain($newFileName);

		}

	}

	public function createMenuAction()
	{
        $errcode    = 0;
        $errmsg = '';
        $data = '';

		$menuList       = System_Lib_App::getRequest('menuList', System_Lib_Request::TYPE_ARRAY);
		$PubicAccountId = System_Lib_App::getRequest('publicAccountId', System_Lib_Request::TYPE_INT, 1);

		try {
            $menu = new XP_BModel_Menu();
            $menu->updateMenu($PubicAccountId, $menuList);

            $menu          = new XP_BModel_Menu();
            $menus         = $menu->getMenu($PubicAccountId);
            $menusXml      = $menu->getMenuJson($menus);
            $config        = System_Lib_App::app()->getConfig('wxConfig');
            $publicAccount = XP_BModel_PublicAccount::getPublicAccount($PubicAccountId);
            if (empty($publicAccount)) throw new Exception("PublicAccount not found,publicAccountId:" . $PubicAccountId);

			$weixin = new XP_Lib_Weixin();
            $result=$weixin->createMenu($menusXml, $publicAccount->id, $publicAccount->appId, $publicAccount->appSecret);
            $errcode=$result['errcode'];
            $errmsg=$result['errmsg'];
            $data=$result['data'];
		} catch (Exception $e) {
            $errcode    = "9999";
            $errmsg = "Create menu error.Error: " . $e->getMessage();
		}

		echo XP_Lib_Utility::jsonResult($errcode, $errmsg, $data);

	}

	public function infoAction()
	{
		$id     = System_Lib_App::app()->getRequest('Id', System_Lib_Request::TYPE_INT);
		$bll    = new XP_BModel_PublicAccount();
		$result = $bll->info($id);
		print_r($result);
	}
}

?>