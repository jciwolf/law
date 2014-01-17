<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-10-31
 * Time: 下午2:36
 * To change this template use File | Settings | File Templates.
 */

class XP_Controller_BaseReplyAjax extends XP_Controller_BaseAjax
{
	protected $layoutName = 'MP_Layout_Blank';

	public function getAction()
	{

		$outPut    = "N";
		$ReplyType = System_Lib_App::getPost('ReplyType', System_Lib_Request::TYPE_INT);
		$accountId = System_Lib_App::getPost('accountId', System_Lib_Request::TYPE_INT);
		if (!empty($ReplyType)) {
			$r = XP_BModel_Reply::getReply($ReplyType, $accountId);
			if (!empty($r)) {
				$outPut = json_encode($r);

			}
		}
		$this->renderPlain($outPut);
	}

	public function UpdateAction()
	{
		$outPut           = "N";
		$errorCode        = 0;
		$errorMessage     = '';
		$content          = System_Lib_App::getPost('content', System_Lib_Request::TYPE_STRING);
		$ReplyType        = System_Lib_App::getPost('ReplyType', System_Lib_Request::TYPE_INT);
		$ReplyMessageType = System_Lib_App::getPost('ReplyMessageType', System_Lib_Request::TYPE_INT);
		$accountId        = System_Lib_App::getPost('accountId', System_Lib_Request::TYPE_INT);
		//var_dump(json_decode($content, true));
		if (!empty($ReplyType)) {
			$c=json_decode($content, true);
			$r = XP_BModel_Reply::save($c, $ReplyType, $ReplyMessageType, $accountId);
			if (!empty($r)) {
				//updateload media
				$mediaId="";
				if($ReplyMessageType==3)
				$mediaId=$this->uploadToWeiXin($accountId,$c["file"],XP_Lib_Weixin::MEDIA_TYPE_VOICE);
				else if($ReplyMessageType==4)
				$mediaId=$this->uploadToWeiXin($accountId,$c["file"],XP_Lib_Weixin::MEDIA_TYPE_VIDEO);
				if(!empty($mediaId))
				{
					$c["mediaId"]=$mediaId;
					$c["type"]=$ReplyMessageType;
					$r=XP_BModel_Reply::save($c, $ReplyType, $ReplyMessageType, $accountId);
				}

				$outPut = $r;
			}
		}
		echo XP_Lib_Utility::jsonResult($errorCode, $errorMessage, $outPut);
	}

	public function deleteAction()
	{
		$id = System_Lib_App::getPost('id', System_Lib_Request::TYPE_INT);
		if (!empty($id)) {
			$r = WTuan_Model_CommonBanner::dataAccess()->filter(WTuan_Model_CommonBanner::ID, $id)->findOne();
			if ($r) {
				try {
					$cacheKey = WTuan_Lib_KeyManager::GetBannerKey($r->type);
					$server   = System_Lib_App::app()->getConfig('memcache');
					System_Lib_Memcache::Delete($cacheKey, $server);
				} catch (Exception $ex) {

				}
			}
			WTuan_Model_CommonBanner::dataAccess()->filter(WTuan_Model_CommonBanner::ID, $id)
				->delete();

			$this->renderPlain("Y");

		}
	}

//活动内容文件添加
	public function uploaderAction()
	{

		// Define a destination

		//var_dump($_POST);
		//var_dump($_GET);
	    $imageConfig=	System_Lib_App::app()->getConfig('attachment');
		if(is_array($imageConfig)==false)
		{

			$this->renderPlain("false");
			return;
		}

		$targetPath =dirname(dirname(__FILE__)) ."/Public". $imageConfig["path"];;
		//var_dump($targetFolder);
		//return;
		//$BannerType   = System_Lib_App::app()->getPost("BannerType", System_Lib_Request::TYPE_INT, 0);
		if (!empty($_FILES)) {

			//check

			$date           = date("Y/m/d");
			$filenamePrefix = $date . "/" . time() . "_" . rand(100, 999);
			$tempFile       = $_FILES['Filedata']['tmp_name'];
			$ext            = strtolower(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION));
		   //var_dump($activityId);

			$newFileName = $filenamePrefix . "." . $ext;

			$targetFile = rtrim($targetPath, '/') . '/' . $newFileName;

			//move_uploaded_file($tempFile,$targetFile);

           // var_dump($tempFile);
            //var_dump($targetFile);
			if (System_Lib_File::copy($tempFile, $targetFile)) {
				//同步目录
				$syncCmd = '/data/soft/wmobile.rsync.sh';

				if (file_exists($syncCmd)) {
					$syncCmd .= ' ' . str_replace(System_Lib_App::app()->getConfig('mediaRoot'), '', dirname($targetFile));
					$str = System_Lib_Utils::system($syncCmd);
					//debug($str);
				}
				$this->renderPlain($newFileName);
			} else {
				$this->renderPlain("false");
			}

		}

	}

	private  function uploadToWeiXin($publicAccountId,$fileName,$mediaType)
	{
		//$publicAccountId        = System_Lib_App::getPost('accountId', System_Lib_Request::TYPE_INT);
		//$fileName        = System_Lib_App::getPost('fileName', System_Lib_Request::TYPE_STRING);
		//var_dump("$publicAccountId.fielname:$fileName.mediaType:$mediaType");
		$publicAccount=XP_BModel_PublicAccount::getPublicAccount($publicAccountId);

		if(true)
		{
			//var_dump($publicAccount);
			$wxconfig = array('appId'=>$publicAccount->appId, 'appSecret'=>$publicAccount->appSecret, 'token'=>$publicAccount->token, 'publicAccountId'=>$publicAccountId);
			//var_dump("wxconfig:$wxconfig");
			$weixin = XP_Lib_Weixin::portal($wxconfig);
			$imageConfig=	System_Lib_App::app()->getConfig('attachment');
			//var_dump("$imageConfig:imageConfig");
			if(is_array($imageConfig)==false)
			{

				return false;
			}

			$targetPath =dirname(dirname(__FILE__)) ."/Public". $imageConfig["path"];
			$r=$weixin->updateMedia($mediaType,$targetPath.$fileName);

			if(isset($r["errorcode"]))
			{
				//error
			}
			else
			{
				$mediaId=$r["media_id"];
				return $mediaId;
			}

		}

	}

}

?>